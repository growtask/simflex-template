<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Menu;
use Simflex\Core\Admin\Page;
use Simflex\Core\Admin\WidgetBase;
use Simflex\Core\Http\Html\HtmlRequest;
use Simflex\Core\Models\User;
use Simflex\Core\System\Container;
use Simflex\Core\System\ExtensionConfig;
use Simflex\Core\System\Privilege;
use Tests\Support\MockDb;

/**
 * Виджет по запросу ?sfwidget=id: только активный, существующий и доступный пользователю по привилегии виджета
 */
class WidgetRequestTest extends Unit
{
    use MockDb;

    private mixed $requestBackup = null;
    private mixed $userBackup = null;

    protected function _before(): void
    {
        $this->requestBackup = Container::getRequest();
        $this->userBackup = Container::get('user');
        ExtensionConfig::reset([]);
        Menu::reset();
    }

    protected function _after(): void
    {
        Container::set('request', $this->requestBackup);
        Container::set('user', $this->userBackup);
        ExtensionConfig::reset();
        Menu::reset();
        Container::set('page', null);
    }

    public function testWidgetWithoutPrivilegeIsShown(): void
    {
        $this->assertSame(
            'probe-widget',
            $this->requestWidget(['widget_id' => 5, 'privilege' => ''], [Privilege::ADMIN])
        );
    }

    public function testWidgetOfUserPrivilegeIsShown(): void
    {
        $this->assertSame('probe-widget', $this->requestWidget(
            ['widget_id' => 5, 'privilege' => 'manager'],
            [Privilege::ADMIN, 'manager']
        ));
    }

    public function testWidgetOfOtherPrivilegeIsEmpty(): void
    {
        $this->assertSame('', $this->requestWidget(['widget_id' => 5, 'privilege' => 'manager'], [Privilege::ADMIN]));
    }

    public function testMissingOrInactiveWidgetIsEmpty(): void
    {
        // неактивный виджет запрос (active = 1) не находит, как и несуществующий
        $this->assertSame('', $this->requestWidget(null, [Privilege::ADMIN]));
        // класс, который не является виджетом
        $this->assertSame('', $this->requestWidget(
            ['widget_id' => 5, 'privilege' => '', 'class' => \stdClass::class],
            [Privilege::ADMIN]
        ));
    }

    /**
     * Ajax-запрос /admin?sfwidget=5 пользователя с привилегиями; запрос виджета возвращает $widgetRow
     *
     * @param list<string> $privileges
     * @return string Содержимое страницы
     */
    private function requestWidget(?array $widgetRow, array $privileges): string
    {
        $widgetQueries = [];
        $db = $this->mockDb();
        $lastQuery = '';
        $db->method('query')->willReturnCallback(function (string $q, array $params = []) use (
            &$lastQuery,
            &$widgetQueries
        ) {
            $lastQuery = $q;
            if (str_contains($q, 'admin_widget')) {
                $widgetQueries[] = [$q, $params];
            }
            return true;
        });
        $isFetched = false;
        $db->method('fetch')->willReturnCallback(function () use (&$lastQuery, &$isFetched, $widgetRow) {
            if ($widgetRow === null || $isFetched || !str_contains($lastQuery, 'admin_widget')) {
                return false;
            }
            $isFetched = true;
            return $widgetRow
                + ['class' => ProbeWidget::class, 'name' => 'Probe', 'param' => '', 'npp' => 0, 'active' => 1];
        });

        Container::getConfig()->adminPath = '/admin';
        Container::set('request', HtmlRequest::create('/admin', ['sfwidget' => '5', 'ajax' => '1']));
        Container::set('user', new class ($privileges) extends User {
            /** @param list<string> $names */
            public function __construct(private array $names)
            {
            }

            public function getPrivileges(): array
            {
                return array_combine($this->names, $this->names);
            }
        });

        $page = new Page();
        Container::set('page', $page);
        $page->init();

        $this->assertCount(1, $widgetQueries);
        $this->assertContains(5, $widgetQueries[0][1]);
        return $page->getContent();
    }
}

class ProbeWidget extends WidgetBase
{
    protected function content(): string
    {
        return 'probe-widget';
    }
}
