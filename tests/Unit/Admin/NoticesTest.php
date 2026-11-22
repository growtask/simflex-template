<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Notice;
use Simflex\Core\Admin\NoticeLevel;
use Simflex\Core\Admin\Notices;
use Simflex\Core\Admin\NoticeSource;
use Simflex\Core\Admin\Page;
use Simflex\Core\Auth\SessionStorage;
use Simflex\Core\System\Container;
use Simflex\Core\System\ExtensionConfig;
use Simflex\Core\System\Session;

/**
 * Уведомления админки (ExtensionConfig::$adminNoticeSources): сбор, вывод под заголовком, скрытие в сессии
 */
class NoticesTest extends Unit
{
    private mixed $configBackup = null;
    private mixed $dismissedBackup = null;

    protected function _before(): void
    {
        // Codeception уже вывел заголовок в CLI: подавляем только предупреждения PHP о сессии
        set_error_handler(static fn(int $level, string $message) => str_starts_with($message, 'session_'));
        $this->dismissedBackup = Session::get(Notices::SESSION_KEY);
        Session::set(Notices::SESSION_KEY, null);
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin', 'adminLogo' => []]);
        ExtensionConfig::reset([]);
        NoticeSourceStub::$notices = [];
        NoticeSourceStub::$calls = 0;
    }

    protected function _after(): void
    {
        try {
            Session::set(Notices::SESSION_KEY, $this->dismissedBackup);
            Container::set('config', $this->configBackup);
            ExtensionConfig::reset();
            Container::set('page', new Page());
        } finally {
            restore_error_handler();
        }
    }

    public function testNoSourcesNoBlock(): void
    {
        $page = new Page();

        $this->assertSame([], Notices::getVisible());
        $this->assertStringNotContainsString('admin-notices', $page->drawPageHead('Title'));
    }

    public function testNoticeIsDrawnUnderPageHeadWithDismissAttributes(): void
    {
        NoticeSourceStub::$notices = [
            new Notice('stub_error', NoticeLevel::Error, 'Нет <бэкапа>', 'Базы: a, b', '/admin/db', 'К базам'),
        ];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);

        $html = (new Page())->drawPageHead('Title');

        $this->assertStringContainsString('<div class="admin-notices">', $html);
        $this->assertLessThan(strpos($html, 'admin-notices'), strpos($html, 'page-head__row'), 'notices go after page-head');
        $this->assertStringContainsString('notice_style_error', $html);
        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('Нет &lt;бэкапа&gt;', $html);
        $this->assertStringContainsString('Базы: a, b', $html);
        $this->assertStringContainsString('data-sf-notice-key="stub_error"', $html);
        $this->assertStringContainsString('data-sf-notice-dismiss="/admin/notice-dismiss"', $html);
        $this->assertStringContainsString('data-sf-notice-close', $html);
        $this->assertStringContainsString('href="/admin/db"', $html);
        $this->assertStringContainsString('К базам', $html);
    }

    public function testNoticesAreDrawnOncePerRequest(): void
    {
        NoticeSourceStub::$notices = [new Notice('stub_info', NoticeLevel::Info, 'Info')];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);
        $page = new Page();

        $first = $page->drawPageHead('A');
        $second = $page->drawPageHead('B');

        $this->assertStringContainsString('notice_style_info', $first);
        $this->assertStringNotContainsString('admin-notices', $second);
        $this->assertSame(1, NoticeSourceStub::$calls);
    }

    public function testLevelsMapToStyles(): void
    {
        NoticeSourceStub::$notices = [
            new Notice('a', NoticeLevel::Warning, 'W'),
            new Notice('b', NoticeLevel::Info, 'I'),
        ];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);

        $html = (new Page())->drawNotices();

        $this->assertStringContainsString('notice_style_alert', $html);
        $this->assertStringContainsString('notice_style_info', $html);
    }

    public function testDismissHidesNoticeForSession(): void
    {
        NoticeSourceStub::$notices = [new Notice('stub', NoticeLevel::Error, 'Нет бэкапа', 'a')];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);

        $this->assertCount(1, Notices::getVisible());
        $this->assertTrue(Notices::dismiss('stub'));
        $this->assertSame([], Notices::getVisible());
        $this->assertSame('', (new Page())->drawNotices());

        // новая сессия: скрытие стёрто, уведомление снова видно
        Session::set(Notices::SESSION_KEY, null);
        $this->assertCount(1, Notices::getVisible());
    }

    public function testLoginAndLogoutResetDismissedNotices(): void
    {
        NoticeSourceStub::$notices = [new Notice('stub', NoticeLevel::Error, 'Нет бэкапа')];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);
        $userId = SessionStorage::get();
        try {
            Notices::dismiss('stub');
            $this->assertSame([], Notices::getVisible());

            SessionStorage::set($userId);

            $this->assertCount(1, Notices::getVisible());
        } finally {
            SessionStorage::set($userId);
        }
    }

    public function testChangedNoticeIsShownAgain(): void
    {
        NoticeSourceStub::$notices = [new Notice('stub', NoticeLevel::Error, 'Нет бэкапа', 'a')];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);
        Notices::dismiss('stub');

        NoticeSourceStub::$notices = [new Notice('stub', NoticeLevel::Error, 'Нет бэкапа', 'a, b')];

        $this->assertCount(1, Notices::getVisible());
    }

    public function testDismissUnknownKey(): void
    {
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);

        $this->assertFalse(Notices::dismiss('unknown'));
        $this->assertNull(Session::get(Notices::SESSION_KEY));
    }

    public function testDuplicateKeyKeepsFirstNotice(): void
    {
        NoticeSourceStub::$notices = [
            new Notice('same', NoticeLevel::Error, 'First'),
            new Notice('same', NoticeLevel::Info, 'Second'),
        ];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);

        $visible = Notices::getVisible();

        $this->assertCount(1, $visible);
        $this->assertSame('First', $visible[0]->title);
    }

    public function testBrokenSourceDoesNotBreakOthers(): void
    {
        NoticeSourceStub::$notices = [new Notice('ok', NoticeLevel::Info, 'Ok')];
        ExtensionConfig::reset([$this->extension([NoticeSourceBrokenStub::class, NoticeSourceStub::class])]);
        $loggers = new \ReflectionProperty(\Simflex\Core\Logger\Log::class, 'loggers');
        $previous = $loggers->getValue();
        $logger = new class () extends \Psr\Log\AbstractLogger {
            /** @var list<string> */
            public array $errors = [];

            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->errors[] = $level . ': ' . strtr((string)$message, array_combine(
                    array_map(fn($name) => '{' . $name . '}', array_keys($context)),
                    array_map('strval', $context),
                ));
            }
        };
        $loggers->setValue(null, [$logger]);
        try {
            $visible = Notices::getVisible();
        } finally {
            $loggers->setValue(null, $previous);
        }

        $this->assertCount(1, $visible);
        $this->assertCount(1, $logger->errors);
        $this->assertStringContainsString('error: ', $logger->errors[0]);
        $this->assertStringContainsString('source failed', $logger->errors[0]);
    }

    public function testNoticesAreNotDrawnInAjax(): void
    {
        NoticeSourceStub::$notices = [new Notice('stub', NoticeLevel::Info, 'Info')];
        ExtensionConfig::reset([$this->extension([NoticeSourceStub::class])]);
        $page = new Page();
        (new \ReflectionProperty(Page::class, 'ajax'))->setValue($page, true);

        $this->assertSame('', $page->drawNotices());
    }

    /**
     * @param list<class-string<NoticeSource>> $sources
     */
    private function extension(array $sources): ExtensionConfig
    {
        return new class ($sources) extends ExtensionConfig {
            public function __construct(array $sources)
            {
                $this->name = 'test_notices';
                $this->adminNoticeSources = $sources;
            }
        };
    }
}

class NoticeSourceStub implements NoticeSource
{
    /** @var list<Notice> */
    public static array $notices = [];
    public static int $calls = 0;

    public function getNotices(): array
    {
        self::$calls++;
        return self::$notices;
    }
}

class NoticeSourceBrokenStub implements NoticeSource
{
    public function getNotices(): array
    {
        throw new \RuntimeException('source failed');
    }
}
