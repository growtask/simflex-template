<?php

namespace Tests\Unit\Admin;

use App\Core\Privilege;
use Codeception\Test\Unit;
use Simflex\Admin\Core as AdminCore;
use Simflex\Admin\Menu;
use Simflex\Admin\MenuItem;
use Simflex\Admin\Structure\Handlers;
use Simflex\Core\Container;
use Simflex\Core\Models\User;
use Tests\Support\MockDb;

/**
 * The admin panel menu declared in code (\Simflex\Admin\Menu, App\Admin\Menu)
 */
class MenuTest extends Unit
{
    use MockDb;

    private array $serverBackup = [];
    private mixed $userBackup = null;

    protected function _before(): void
    {
        $this->serverBackup = $_SERVER;
        $this->userBackup = Container::isSet('user') ? Container::get('user') : null;
        Menu::reset();
    }

    protected function _after(): void
    {
        $_SERVER = $this->serverBackup;
        Container::set('user', $this->userBackup);
        Menu::reset();
        $this->resetAdminCore();
    }

    public function testCoreSections(): void
    {
        Container::getConfig()->adminMenuClass = \App\Admin\Menu::class;

        $this->assertSame([
            '/account' => '',
            '/admin' => '',
            '/admin/privilege' => '/admin',
            '/admin/role' => '/admin',
            '/admin/role_privilege' => '/admin',
            '/admin/user' => '/admin',
            '/admin/privilege_personal' => '/admin',
            '/cron' => '/admin',
            '/site' => '',
            '/site/menu' => '/site',
            '/site/module' => '/site',
            '/site/settings' => '/site',
            '/site/seo' => '/site',
            '/content' => '',
        ], array_map(fn($link) => Menu::parent($link), array_combine(array_keys(Menu::all()), array_keys(Menu::all()))));

        $this->assertSame(\App\Admin\Menu::class, Menu::registryClass());
        $this->assertSame(Handlers\UserRole::class, Menu::all()['/admin/role']->handler);
        $this->assertSame(Privilege::DEV, Menu::all()['/admin']->privilege);
        $this->assertSame(Privilege::ADMIN, Menu::all()['/admin/role']->privilege);
        $this->assertTrue(Menu::all()['/account']->hidden);
    }

    public function testAppAddsAndRemovesItems(): void
    {
        Container::getConfig()->adminMenuClass = TestMenu::class;

        $all = Menu::all();
        $this->assertSame(
            ['/account', '/admin', '/site', '/blog', '/content', '/shop'],
            array_keys(array_filter($all, fn($item) => Menu::parent($item->link) === ''))
        );
        $this->assertArrayNotHasKey('/site/seo', $all);
        $this->assertArrayNotHasKey('/cron', $all);
        $this->assertSame('/site', Menu::parent('/site/redirect'));
        $this->assertSame('/shop', Menu::parent('/shop/order'));
        // replaced in place
        $this->assertSame('Страницы', $all['/content']->name);
    }

    public function testUnknownParent(): void
    {
        Container::getConfig()->adminMenuClass = BrokenMenu::class;
        $this->expectException(\LogicException::class);
        Menu::all();
    }

    public function testPanelShowsItemsOfUserPrivileges(): void
    {
        $this->mockRows([]);
        Container::getConfig()->adminPath = '/admin';
        Container::getConfig()->adminMenuClass = TestMenu::class;
        $_SERVER['REQUEST_URI'] = '/admin/site/redirect';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $this->resetAdminCore();
        Container::set('user', new class () extends User {
            public function getPrivileges(): array
            {
                return ['admin' => 'admin'];
            }
        });

        AdminCore::init();

        $menu = AdminCore::menu();
        // the dev group and the manager section aren't shown
        $this->assertSame(['/account', '/site', '/blog', '/content'], array_keys($menu['']));
        $this->assertArrayNotHasKey('/shop/order', $menu['/shop'] ?? []);
        // the subitems of the dev group are, as before: the sections are open by their links
        $this->assertArrayHasKey('/admin/user', $menu['/admin']);
        $this->assertSame('/admin/site/redirect', AdminCore::menuCurItem('link'));
        // a table section gets the table name from the registry, a section class has none
        $this->assertSame('content', $menu['']['/content']['table']);
        $this->assertSame('', $menu['']['/account']['table']);
        $this->assertSame([
            ['link' => '/admin/site', 'name' => 'Настройки сайта'],
            ['link' => '/admin/site/redirect', 'name' => 'Редиректы'],
        ], AdminCore::crumbs());
    }

    private function resetAdminCore(): void
    {
        foreach (['menuTree' => [], 'menuById' => [], 'menuCur' => false, 'crumbs' => []] as $name => $value) {
            (new \ReflectionProperty(AdminCore::class, $name))->setValue(null, $value);
        }
    }
}

class TestMenu extends \App\Admin\Menu
{
    protected static function init(): void
    {
        parent::init();
        static::add(new MenuItem('Магазин', '/shop', privilege: Privilege::MANAGER, children: [
            new MenuItem('Заказы', '/shop/order', handler: 'App\\Admin\\Order', privilege: Privilege::MANAGER),
        ]));
        static::add(new MenuItem('Блог', '/blog', handler: 'App\\Admin\\Blog'), before: '/content');
        static::add(new MenuItem('Редиректы', '/site/redirect', handler: 'App\\Admin\\Redirect'), parent: '/site');
        static::add(new MenuItem('Страницы', '/content', handler: Handlers\Content::class));
        static::remove('/site/seo');
        static::remove('/cron');
    }
}

class BrokenMenu extends Menu
{
    protected static function init(): void
    {
        static::add(new MenuItem('Блог', '/blog/post'), parent: '/blog');
    }
}
