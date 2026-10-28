<?php

namespace Tests\Unit\Extensions;

use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Core\SiteMenu;
use Simflex\Http\Html\HtmlRequest;
use Simflex\Extensions\Menu\Menu;

/**
 * Модуль «Меню» ядра: дерево пунктов, активный раздел, подпункты текущего раздела
 */
class MenuModuleTest extends Unit
{
    protected mixed $request = null;
    protected ?array $menuTree = null;

    protected function _before(): void
    {
        $this->request = Container::isSet('request') ? Container::getRequest() : null;
        $tree = new \ReflectionProperty(SiteMenu::class, 'tree');
        $this->menuTree = $tree->getValue();
        $tree->setValue(null, [
            0 => [
                1 => ['menu_id' => 1, 'link' => '/', 'name' => 'Главная'],
                2 => ['menu_id' => 2, 'link' => '/catalog/', 'name' => 'Каталог'],
                3 => ['menu_id' => 3, 'link' => '/hidden/', 'name' => 'Скрытый', 'hidden' => 1],
            ],
            2 => [
                4 => ['menu_id' => 4, 'link' => '/catalog/phones/', 'name' => 'Телефоны'],
            ],
        ]);
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(SiteMenu::class, 'tree'))->setValue(null, $this->menuTree);
        Container::set('request', $this->request);
    }

    protected function draw(string $path, array $params = []): string
    {
        $_SERVER['REQUEST_URI'] = $path;
        try {
            Container::set('request', new HtmlRequest());
        } finally {
            unset($_SERVER['REQUEST_URI']);
        }

        $module = new Menu([
            'item_id' => 1,
            'class' => Menu::class,
            'name' => 'Меню',
            'posname' => 'top',
            'params' => serialize($params),
        ]);

        return preg_replace('/\s+/', ' ', $module->execute());
    }

    public function testFirstLevelWithActiveSection(): void
    {
        $html = $this->draw('/catalog/phones/');

        $this->assertStringContainsString('<a href="/">Главная</a>', $html);
        $this->assertStringContainsString('<a class="active" href="/catalog/">Каталог</a>', $html);
        $this->assertStringNotContainsString('Скрытый', $html);
        $this->assertStringNotContainsString('Телефоны', $html);
    }

    public function testNestedLevels(): void
    {
        $html = $this->draw('/', ['max_level' => 1]);

        $this->assertStringContainsString('<a class="active" href="/">Главная</a>', $html);
        $this->assertStringContainsString('<a class="has-children" href="/catalog/">Каталог</a> <ul class="level-1">', $html);
        $this->assertStringContainsString('Телефоны', $html);
    }

    public function testChildrenOfCurrentSection(): void
    {
        $html = $this->draw('/catalog/phones/', ['is_child' => 1]);
        $this->assertStringContainsString('<a class="active" href="/catalog/phones/">Телефоны</a>', $html);
        $this->assertStringNotContainsString('Главная', $html);

        // текущая страница вне разделов с подпунктами - меню пустое
        $this->assertSame('', trim($this->draw('/about/', ['is_child' => 1])));
    }

    public function testRegisteredInSite(): void
    {
        $this->assertSame('Меню', \Simflex\Core\ExtensionRegistry::module(\App\Extensions\Menu\Menu::class)['name'] ?? null);
    }
}
