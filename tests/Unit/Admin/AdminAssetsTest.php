<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Page;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\ButtonTheme;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\Layout as Button;
use Simflex\Core\Http\Request;
use Simflex\Core\Layout\LayoutManager;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Статика админки нового дизайна: стили (корневой стиль админки, px на десктопе, отдельный кеш), спрайт иконок,
 * скрипты-модули
 */
class AdminAssetsTest extends Unit
{
    private mixed $configBackup;
    private mixed $pageBackup;

    protected function _before(): void
    {
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        $this->pageBackup = Container::isSet('page') ? Container::get('page') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => false]);
        Container::set('location', Location::Admin);
        Container::set('page', null);
    }

    protected function _after(): void
    {
        // getMeta() собирает стили в кеш: убрать файлы, созданные тестом
        foreach (glob(Container::getPaths()->getPublic() . '/cache/css/admin-*.css') ?: [] as $file) {
            unlink($file);
        }
        Container::set('location', Location::Site);
        Container::set('config', $this->configBackup);
        Container::set('page', $this->pageBackup);
    }

    public function testAdminStylesCompiledToOwnCacheInPixels(): void
    {
        // компонент Layout, выведенный в админке, попадает в стили админки
        Button::drawButton(text: 'Сохранить');
        $cachePath = LayoutManager::init();
        $file = Container::getPaths()->getPublic() . $cachePath;
        try {
            $this->assertStringStartsWith('/cache/css/admin-', $cachePath);
            $css = (string)file_get_contents($file);
            // десктоп в px (vw() админки), планшет и телефон в vw
            $this->assertStringContainsString('margin-inline:250px', $css);
            $this->assertStringContainsString('margin-inline:45px', $css);
            $this->assertMatchesRegularExpression('/margin-inline:\s*2\.08\d*vw/', $css);
            // токены и стили компонента
            $this->assertStringContainsString('--color-main-gray-black', $css);
            $this->assertStringContainsString('.btn', $css);
            // собранный файл подключается в head страницы админки
            $this->assertStringContainsString($cachePath, Page::get()->getMeta());
        } finally {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function testRenderIconUsesAdminSprite(): void
    {
        $html = renderIcon('check', 'menu__icon');

        $this->assertStringContainsString('class="menu__icon"', $html);
        $this->assertMatchesRegularExpression('#href="[^"]*/Admin/theme/img/icons\.svg(\?v=\d+)?\#icon-check"#', $html);

        Container::set('location', Location::Site);
        $this->assertStringContainsString('href="/assets/default/icons/icons.svg#icon-check"', renderIcon('check'));
    }

    public function testAdminSpriteHasDesignSystemIcons(): void
    {
        $sprite = (string)file_get_contents(Page::vendorPath() . '/theme/img/icons.svg');
        $ids = \Simflex\Core\Admin\Handlers\Ui::getIconIds();

        // ID из каталога дизайн-системы, без дублей
        foreach (['check', 'chevron-s-down', 'notify-info', 'setting', 'sun-moon', 'dots-vertical'] as $id) {
            $this->assertContains($id, $ids);
        }
        $this->assertSame(count($ids), count(array_unique($ids)));
        // у каждого символа свой viewBox, цвет только наследуется
        $this->assertSame(count($ids), substr_count($sprite, 'viewBox="0 0 24 24"'));
        preg_match_all('/fill="([^"]+)"/', $sprite, $fills);
        $this->assertSame(['currentColor'], array_values(array_unique($fills[1])));
    }

    public function testThemeFromCookie(): void
    {
        $requestBackup = Container::isSet('request') ? Container::get('request') : null;
        try {
            $cases = [
                [[], Page::THEME_LIGHT],
                [[Page::THEME_COOKIE => 'dark'], Page::THEME_DARK],
                [[Page::THEME_COOKIE => 'unknown'], Page::THEME_LIGHT],
            ];
            foreach ($cases as [$cookies, $theme]) {
                Container::set('request', Request::create('/admin', cookies: $cookies));
                // значение темы - значение enum темы компонентов Layout
                $this->assertSame($theme, ButtonTheme::from(Page::get()->getTheme())->value);
            }
        } finally {
            Container::set('request', $requestBackup);
        }
    }

    public function testLogoFromConfig(): void
    {
        $requestBackup = Container::isSet('request') ? Container::get('request') : null;
        try {
            Container::set('request', Request::create('/admin'));
            // без настройки сайта — лого Simflex CMS из статики ядра
            Container::getConfig()->adminLogo = ['name' => 'Simflex CMS', 'src' => '', 'srcDark' => ''];
            $html = Page::get()->drawLogo('menu__logo', '/admin', isHomeLink: true);
            $this->assertStringContainsString('aria-label="Simflex CMS — главная админки"', $html);
            $this->assertMatchesRegularExpression('#src="[^"]*/Admin/theme/img/brand/logo\.svg#', $html);

            // лого сайта: светлое и тёмное из статики темы сайта
            Container::getConfig()->adminLogo = [
                'name' => 'Growtask Hosting',
                'src' => 'images/brand/admin-logo.svg',
                'srcDark' => 'images/brand/admin-logo-dark.svg',
            ];
            $html = Page::get()->drawLogo('auth__logo', '/');
            $this->assertStringContainsString('aria-label="Growtask Hosting"', $html);
            $this->assertStringContainsString('src="/assets/default/images/brand/admin-logo.svg', $html);

            Container::set('request', Request::create('/admin', cookies: [Page::THEME_COOKIE => 'dark']));
            $html = Page::get()->drawLogo('auth__logo', '/');
            $this->assertStringContainsString('src="/assets/default/images/brand/admin-logo-dark.svg', $html);
        } finally {
            Container::set('request', $requestBackup);
        }
    }

    public function testCoreJsModuleIsModuleScript(): void
    {
        $page = Page::get();
        $page->coreJsModule('/theme/js/admin.js');

        $this->assertMatchesRegularExpression(
            '#<script type="module" src="[^"]*/Admin/theme/js/admin\.js"></script>#',
            $page->getMeta()
        );
    }
}
