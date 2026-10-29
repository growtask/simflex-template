<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Common\Header\Layout as Header;
use Simflex\Core\Layout\Components\Common\HeaderBanner\Layout as HeaderBanner;
use Simflex\Core\Layout\Components\Common\MobileMenu\Layout as MobileMenu;
use Simflex\Core\System\Container;

/**
 * Отрисовка шапки: строка над шапкой, Header, MobileMenu, пункты меню из дерева таблицы menu
 */
class HeaderComponentsTest extends Unit
{
    private mixed $config = null;

    protected function _before(): void
    {
        $this->config = Container::get('config');
        Container::set('config', (object)['theme' => 'default']);
    }

    protected function _after(): void
    {
        Container::set('config', $this->config);
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function getItems(): array
    {
        return [
            ['text' => 'Главная', 'link' => '/', 'active' => true, 'children' => []],
            ['text' => 'Информация', 'link' => '/info', 'active' => false, 'children' => [
                ['text' => 'FAQ', 'link' => '/faq', 'active' => false],
            ]],
            ['text' => 'A & B', 'link' => '/ab', 'active' => false, 'children' => []],
        ];
    }

    public function testHeaderBannerEmptyTextOutputsNothing(): void
    {
        $this->assertSame('', HeaderBanner::drawHeaderBanner(text: '  '));
    }

    public function testHeaderBannerRegionButtonCloseAndEscaping(): void
    {
        $html = HeaderBanner::drawHeaderBanner(
            text: 'Скидка <b>10%</b>',
            buttonText: 'Подробнее',
            buttonLink: '/sale'
        );

        $this->assertStringContainsString('role="region"', $html);
        $this->assertStringContainsString('data-header-banner="' . HeaderBanner::getKey('Скидка <b>10%</b>', 'Подробнее', '/sale') . '"', $html);
        $this->assertStringContainsString('Скидка &lt;b&gt;10%&lt;/b&gt;', $html);
        $this->assertStringContainsString('href="/sale"', $html);
        $this->assertStringContainsString('data-header-banner-close', $html);
        $this->assertStringContainsString('aria-label="Закрыть"', $html);
        $this->assertStringContainsString('localStorage', $html);
        $this->assertStringContainsString('catch(e)', $html);
    }

    public function testHeaderBannerWithoutLinkHasNoButton(): void
    {
        $html = HeaderBanner::drawHeaderBanner(text: 'Текст', buttonText: 'Подробнее');
        $this->assertStringNotContainsString('header-banner__button', $html);
    }

    public function testHeaderBannerKeyChangesWithContent(): void
    {
        $this->assertNotSame(HeaderBanner::getKey('a'), HeaderBanner::getKey('b'));
    }

    public function testNavUsesTabAndDropdownTab(): void
    {
        $html = Header::drawNav($this->getItems(), 'p');

        $this->assertStringContainsString('class="main-nav ', $html);
        $this->assertStringContainsString('dropdown-tab', $html);
        $this->assertStringContainsString('id="p-1-menu"', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString('A &amp; B', $html);
        $this->assertStringContainsString('href="/faq"', $html);
        $this->assertSame('', Header::drawNav([]));
    }

    public function testHeaderParts(): void
    {
        $html = Header::drawHeader(
            banner: '<div class="my-banner"></div>',
            geo: '<div class="my-geo"></div>',
            items: $this->getItems(),
            logoName: 'Сайт',
            phone: '8 800 555 35 35',
            email: 'a@b.ru',
            socials: ['telegram' => 'https://t.me/x', 'vkontakte' => '', 'unknown' => 'https://x'],
        );

        $this->assertStringContainsString('my-banner', $html);
        // Слот города: в основной шапке (не в закреплённой копии) и в мобильном меню
        $this->assertSame(1, substr_count($html, 'header__geo'));
        $this->assertSame(1, substr_count($html, 'mobile-menu__geo'));
        $this->assertStringContainsString('header__top', $html);
        $this->assertStringContainsString('header__bottom', $html);
        $this->assertStringContainsString('header__line', $html);
        $this->assertStringContainsString('header--fixed', $html);
        $this->assertStringContainsString('data-header-fixed', $html);
        $this->assertStringContainsString('href="tel:88005553535"', $html);
        $this->assertStringContainsString('href="mailto:a@b.ru"', $html);
        $this->assertStringContainsString('https://t.me/x', $html);
        $this->assertStringNotContainsString('social-network_vkontakte', $html);
        $this->assertStringContainsString('data-modal="callback"', $html);
        $this->assertStringContainsString('Связаться с нами', $html);
        $this->assertStringContainsString('data-mobile-menu-open="mobile-menu"', $html);
        // Мобильное меню выводится один раз
        $this->assertSame(1, substr_count($html, 'data-mobile-menu hidden'));
        // Идентификаторы подменю основной и закреплённой шапки различаются
        $this->assertStringContainsString('id="header-nav-1-menu"', $html);
        $this->assertStringContainsString('id="header-fixed-nav-1-menu"', $html);
    }

    public function testHeaderWithoutButtonAndContacts(): void
    {
        $html = Header::drawHeader(buttonText: '');
        $this->assertStringNotContainsString('header__cta', $html);
        $this->assertStringNotContainsString('header__contacts', $html);
        $this->assertStringNotContainsString('main-nav', $html);
    }

    public function testMobileMenuDialogAndSubmenu(): void
    {
        $html = MobileMenu::drawMobileMenu(items: $this->getItems(), phone: '123', geo: '<i class="g"></i>');

        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('data-mobile-menu-close', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('data-mobile-menu-toggle', $html);
        $this->assertStringContainsString('class="g"', $html);
        // Раздел «Информация» стал первой ссылкой подменю
        $this->assertSame(1, substr_count($html, 'href="/info"'));
    }

    public function testGetMenuItemsFromTree(): void
    {
        $tree = [
            0 => [
                1 => ['menu_id' => 1, 'name' => 'Главная', 'link' => '/', 'hidden' => 0],
                2 => ['menu_id' => 2, 'name' => 'Инфо', 'link' => '/info', 'hidden' => 0],
                3 => ['menu_id' => 3, 'name' => 'Скрыт', 'link' => '/hid', 'hidden' => 1],
            ],
            2 => [
                4 => ['menu_id' => 4, 'name' => 'FAQ', 'link' => '/info/faq', 'hidden' => 0],
            ],
        ];

        $home = Header::getMenuItems($tree, 0, 1, '/');
        $this->assertCount(2, $home);
        $this->assertTrue($home[0]['active']);
        $this->assertFalse($home[1]['active']);

        $faq = Header::getMenuItems($tree, 0, 1, '/info/faq');
        $this->assertFalse($faq[0]['active']);
        $this->assertTrue($faq[1]['active']);
        $this->assertTrue($faq[1]['children'][0]['active']);

        $flat = Header::getMenuItems($tree, 0, 0, '/');
        $this->assertSame([], $flat[1]['children']);
    }
}
