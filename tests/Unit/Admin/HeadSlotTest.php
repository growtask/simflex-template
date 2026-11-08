<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\HeadSlot;
use Simflex\Core\Admin\Page;
use Simflex\Core\System\Container;
use Simflex\Core\System\ExtensionConfig;

/**
 * Слоты верхней части админки (ExtensionConfig::$adminHeadSlots): вывод в page-head и topbar
 */
class HeadSlotTest extends Unit
{
    private mixed $configBackup = null;

    protected function _before(): void
    {
        // иконки и ссылки админки читают тему и путь админки из конфига
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => false, 'adminPath' => '/admin', 'adminLogo' => []]);
        ExtensionConfig::reset([]);
    }

    protected function _after(): void
    {
        Container::set('config', $this->configBackup);
        ExtensionConfig::reset();
        Container::set('page', new Page());
    }

    public function testNoSlots(): void
    {
        $page = new Page();

        $this->assertSame('', $page->drawHeadSlots());
        $this->assertStringNotContainsString('page-head__slots', $page->drawPageHead('Title'));
    }

    public function testSlotsInLoadOrder(): void
    {
        ExtensionConfig::reset([
            $this->extension(20, [HeadSlotSecond::class]),
            $this->extension(10, [HeadSlotFirst::class, HeadSlotEmpty::class]),
        ]);
        $page = new Page();

        $this->assertSame('<b>first</b><i>second</i>', $page->drawHeadSlots());

        $head = $page->drawPageHead('Title');
        $this->assertStringContainsString('<div class="page-head__slots"><b>first</b><i>second</i></div>', $head);
        $this->assertLessThan(
            strpos($head, 'page-head__site'),
            strpos($head, 'page-head__slots'),
            'slots go before the «На сайт» button'
        );
    }

    public function testEmptySlotOutputsNoWrapper(): void
    {
        ExtensionConfig::reset([$this->extension(10, [HeadSlotEmpty::class])]);

        $this->assertStringNotContainsString('page-head__slots', (new Page())->drawPageHead('Title'));
    }

    public function testTopbarSlots(): void
    {
        ExtensionConfig::reset([$this->extension(10, [HeadSlotFirst::class])]);
        $page = new Page();
        Container::set('page', $page);

        $html = \Simflex\Core\Http\View::render(
            dirname(__DIR__, 3) . '/vendor/simflex/core/src/Admin/theme/tpl/partial/header.tpl',
            ['page' => $page]
        );

        $this->assertStringContainsString('<div class="topbar__slots"><b>first</b></div>', $html);
    }

    public function testSlotIsDrawnOncePerRequest(): void
    {
        HeadSlotFirst::$calls = 0;
        ExtensionConfig::reset([$this->extension(10, [HeadSlotFirst::class])]);
        $page = new Page();

        $page->drawPageHead('A');
        $page->drawPageHead('B');
        $page->drawHeadSlots();

        $this->assertSame(1, HeadSlotFirst::$calls);
    }

    public function testSlotExceptionIsNotSwallowed(): void
    {
        ExtensionConfig::reset([$this->extension(10, [HeadSlotBroken::class])]);

        $this->expectException(\RuntimeException::class);
        (new Page())->drawHeadSlots();
    }

    /**
     * @param list<class-string<HeadSlot>> $slots
     */
    private function extension(int $loadOrder, array $slots): ExtensionConfig
    {
        return new class ($loadOrder, $slots) extends ExtensionConfig {
            public function __construct(int $loadOrder, array $slots)
            {
                $this->name = 'test_' . $loadOrder;
                $this->loadOrder = $loadOrder;
                $this->adminHeadSlots = $slots;
            }
        };
    }
}

class HeadSlotFirst implements HeadSlot
{
    public static int $calls = 0;

    public function draw(): string
    {
        self::$calls++;
        return '<b>first</b>';
    }
}

class HeadSlotSecond implements HeadSlot
{
    public function draw(): string
    {
        return '<i>second</i>';
    }
}

class HeadSlotEmpty implements HeadSlot
{
    public function draw(): string
    {
        return '';
    }
}

class HeadSlotBroken implements HeadSlot
{
    public function draw(): string
    {
        throw new \RuntimeException('slot failed');
    }
}
