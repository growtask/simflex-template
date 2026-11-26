<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Base\Nav\NavMenu\Layout as NavMenu;
use Simflex\Core\Layout\Components\Base\Nav\NavMenuList\Layout as NavMenuList;
use Simflex\Core\Layout\Components\Base\Nav\NavPagination\Layout as NavPagination;
use Simflex\Core\Layout\Components\Base\Nav\NavPagination\NavPaginationType;
use Simflex\Core\Layout\Components\Base\Seo\Layout as Seo;
use Simflex\Core\Layout\Components\Base\Seo\SeoState;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocialSmall\ButtonSocialSmallNetwork;
use Simflex\Core\Layout\Components\UI\Link\LinkContact\LinkContactType;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Base/Seo и Base/Nav (страница Base в Figma): SEO-текст, список меню, мобильное меню, пагинация «Показать ещё»
 */
class BaseNavTest extends Unit
{
    private mixed $configBackup;

    protected function _before(): void
    {
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => true]);
        Container::set('location', Location::Site);
    }

    protected function _after(): void
    {
        Container::set('config', $this->configBackup);
    }

    public function testSeoColumnsAndCollapsedText(): void
    {
        $columns = [
            ['title' => 'Один <b>', 'text' => '<p>Текст <a href="/a">ссылка</a></p>'],
            ['title' => 'Два', 'text' => '<p>Второй</p>'],
            ['title' => 'Три', 'text' => '<p>Третий</p>'],
            ['title' => 'Четыре', 'text' => '<p>Лишний</p>'],
        ];
        $html = Seo::drawSeo($columns);
        $this->assertStringContainsString('seo_columns_3', $html);
        $this->assertStringNotContainsString('Лишний', $html);
        // свёрнутый текст остаётся в разметке полностью, кнопка помечена aria-expanded
        $this->assertStringNotContainsString('seo_expanded', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('<a href="/a">ссылка</a>', $html);
        $this->assertStringContainsString('Читать далее', $html);
        // заголовок экранируется, текст нет
        $this->assertStringContainsString('Один &lt;b&gt;', $html);
        $this->assertStringContainsString('<h2 class="seo__title">', $html);
        $this->assertStringContainsString('<h3 class="seo__title">', Seo::drawSeo($columns, headingLevel: 3));

        $expanded = Seo::drawSeo([$columns[0]], state: SeoState::Expanded);
        $this->assertStringContainsString('seo_columns_1 seo_expanded', $expanded);
        $this->assertStringContainsString('aria-expanded="true"', $expanded);
        $this->assertStringContainsString('Свернуть', $expanded);
    }

    public function testSeoWithoutMoreIsAlwaysExpandedAndTagsAreLinks(): void
    {
        $html = Seo::drawSeo(
            [['title' => 'Заголовок', 'text' => '<p>Текст</p>']],
            tags: [['name' => 'Диваны', 'link' => '/sofas'], ['name' => 'Кровати', 'link' => '/beds']],
            more: false,
        );
        $this->assertStringContainsString('seo_expanded', $html);
        $this->assertStringNotContainsString('data-sf-seo-toggle', $html);
        $this->assertStringContainsString('<a class="tag tag-gray tag-size_s tag-theme_light" href="/sofas">', $html);
        $this->assertStringContainsString('Популярные запросы', $html);
        $this->assertSame('', Seo::drawSeo([]));
        $this->assertSame('', Seo::drawSeo([['title' => '', 'text' => ' ']]));
    }

    public function testNavMenuListSubmenuAndCurrent(): void
    {
        $items = [
            ['text' => 'Каталог', 'link' => '/catalog', 'current' => true],
            ['text' => 'Информация', 'open' => true, 'children' => [
                ['text' => 'О компании', 'link' => '/about', 'current' => true],
            ]],
            ['text' => 'Блог', 'link' => '/blog'],
        ];
        $html = NavMenuList::drawNavMenuList($items);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringContainsString('aria-expanded="true"', $html);
        $this->assertStringContainsString('nav-menu-list__item_open', $html);
        $this->assertStringContainsString('href="/about"', $html);
        $this->assertSame(1, substr_count($html, 'data-sf-menu-toggle'));
        $closed = NavMenuList::drawNavMenuList([['text' => 'Информация', 'children' => [['text' => 'А', 'link' => '/a']]]]);
        $this->assertStringContainsString('aria-expanded="false"', $closed);
        $this->assertStringNotContainsString('nav-menu-list__item_open', $closed);
        $this->assertSame('', NavMenuList::drawNavMenuList([]));
    }

    public function testNavMenuPartsAreOptional(): void
    {
        $full = NavMenu::drawNavMenu(
            [['text' => 'Каталог', 'link' => '/catalog']],
            location: 'Москва',
            contacts: [
                ['type' => LinkContactType::Phone, 'value' => '+7 (800) 555-35-35'],
                ['type' => LinkContactType::Email, 'value' => 'info@example.com'],
            ],
            socials: [['network' => ButtonSocialSmallNetwork::Telegram, 'href' => 'https://t.me/x']],
            buttonText: 'Связаться',
            buttonLink: '/contacts',
        );
        $this->assertStringContainsString('role="dialog"', $full);
        $this->assertStringContainsString('aria-modal="true"', $full);
        $this->assertStringContainsString('data-sf-nav-menu-close', $full);
        $this->assertStringContainsString('Москва', $full);
        $this->assertStringContainsString('tel:+78005553535', $full);
        $this->assertStringContainsString('mailto:info@example.com', $full);
        $this->assertStringContainsString('https://t.me/x', $full);
        $this->assertStringContainsString('Связаться', $full);

        $bare = NavMenu::drawNavMenu([['text' => 'Каталог', 'link' => '/catalog']], logoHref: '');
        $this->assertStringNotContainsString('nav-menu__location', $bare);
        $this->assertStringNotContainsString('nav-menu__contacts', $bare);
        $this->assertStringNotContainsString('nav-menu__socials', $bare);
        $this->assertStringNotContainsString('nav-menu__button', $bare);
    }

    public function testPaginationMoreType(): void
    {
        $html = NavPagination::drawNavPagination(2, 6, '/list?page={page}', type: NavPaginationType::More, caption: '20 из 120');
        $this->assertStringContainsString('pagination_type_more', $html);
        $this->assertStringContainsString('Показать ещё', $html);
        $this->assertStringContainsString('href="/list?page=3"', $html);
        $this->assertStringContainsString('20 из 120', $html);
        $this->assertStringNotContainsString('pagination__list', $html);
        // последняя порция: кнопка отключена
        $last = NavPagination::drawNavPagination(6, 6, '/list?page={page}', type: NavPaginationType::More);
        $this->assertStringNotContainsString('href="/list?page=7"', $last);
    }
}
