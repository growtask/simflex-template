<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Catalog\Layout\Components\Catalog\Badge\BadgeProduct\BadgeProductStyle;
use Simflex\Catalog\Layout\Components\Catalog\Badge\BadgeProduct\Layout as BadgeProduct;
use Simflex\Catalog\Layout\Components\Catalog\Badge\BadgeStock\BadgeStockStyle;
use Simflex\Catalog\Layout\Components\Catalog\Badge\BadgeStock\BadgeStockTheme;
use Simflex\Catalog\Layout\Components\Catalog\Badge\BadgeStock\Layout as BadgeStock;
use Simflex\Catalog\Layout\Components\Catalog\Card\CardCategory\CardCategoryStyle;
use Simflex\Catalog\Layout\Components\Catalog\Card\CardCategory\Layout as CardCategory;
use Simflex\Catalog\Layout\Components\Catalog\Card\CardSubcategory\Layout as CardSubcategory;
use Simflex\Catalog\Layout\Components\Catalog\Nav\NavCatalog\Layout as NavCatalog;
use Simflex\Catalog\Layout\Components\Catalog\Nav\NavCategories\Layout as NavCategories;
use Simflex\Catalog\Layout\Components\Catalog\Nav\NavCategories\NavCategoriesLevel;
use Simflex\Catalog\Layout\Components\Catalog\Nav\NavMega\Layout as NavMega;
use Simflex\Catalog\Layout\Components\Catalog\Nav\NavMobile\Layout as NavMobile;
use Simflex\Catalog\Layout\Components\Catalog\Search\SearchInput\Layout as SearchInput;
use Simflex\Catalog\Layout\Components\Catalog\Search\SearchList\Layout as SearchList;
use Simflex\Catalog\Layout\Components\Catalog\Search\SearchMain\Layout as SearchMain;
use Simflex\Catalog\Layout\Components\Catalog\Search\SearchProduct\Layout as SearchProduct;
use Simflex\Catalog\Layout\Components\Catalog\Search\SearchQuery\Layout as SearchQuery;
use Simflex\Catalog\Layout\Components\Catalog\Search\SearchQuery\SearchQueryType;
use Simflex\Core\Layout\Components\UI\Badge\BadgeSize;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Компоненты каталога, часть 5a плана дизайн-системы (страница Catalog в Figma): метки, поиск, карточки категорий,
 * меню каталога — разметка, доступность, состав по параметрам
 */
class CatalogComponentsTest extends Unit
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

    public function testBadgesTextIconAndTheme(): void
    {
        $sale = BadgeProduct::drawBadgeProduct('<b>−20 %</b>', BadgeProductStyle::Sale, BadgeSize::Medium, icon: true);
        $this->assertStringContainsString('badge-product_sale', $sale);
        $this->assertStringContainsString('&lt;b&gt;', $sale, 'текст экранируется');
        $this->assertStringContainsString('#icon-check', $sale);
        $this->assertStringContainsString('>Новинка<', BadgeProduct::drawBadgeProduct(style: BadgeProductStyle::New));
        $this->assertStringContainsString('#icon-clapperboard', BadgeProduct::drawBadgeProduct(style: BadgeProductStyle::Video, icon: true));
        $this->assertStringContainsString('#icon-box', BadgeProduct::drawBadgeProduct(style: BadgeProductStyle::ThreeD, icon: true));

        $stock = BadgeStock::drawBadgeStock('предзаказ', BadgeStockStyle::Preorder, theme: BadgeStockTheme::Dark);
        $this->assertStringContainsString('badge-stock_preorder', $stock);
        $this->assertStringContainsString('badge-stock-theme_dark', $stock);
    }

    public function testSearchRowsHighlightAndLinks(): void
    {
        $this->assertSame('<mark class="search-row__match">Тест</mark>ер &lt;розеток&gt;', SearchProduct::highlight('Тестер <розеток>', 'тест'));
        $this->assertSame('Дрель', SearchProduct::highlight('Дрель', 'тест'));

        $row = SearchProduct::drawSearchProduct('Тестер', '/p/1', 'тест', price: '1 ₽', oldPrice: '2 ₽');
        $this->assertStringStartsWith('<a class="search-row search-product', trim($row));
        $this->assertStringContainsString('Старая цена: </span>2 ₽', $row);
        $disabled = SearchProduct::drawSearchProduct('Тестер', '/p/1', disabled: true);
        $this->assertStringNotContainsString('<a ', $disabled);
        $this->assertStringContainsString('aria-disabled="true"', $disabled);

        $recent = SearchQuery::drawSearchQuery('дрель', '/search?q=дрель');
        $this->assertStringContainsString('aria-label="Удалить из истории: дрель"', $recent);
        $this->assertStringContainsString('#icon-clock', $recent);
        $popular = SearchQuery::drawSearchQuery('дрель', '/search?q=дрель', SearchQueryType::Popular, false);
        $this->assertStringContainsString('#icon-trending-up', $popular);
        $this->assertStringNotContainsString('data-sf-search-remove', $popular);
    }

    public function testSearchListStatesAndInput(): void
    {
        $start = SearchList::drawSearchList(recent: ['дрель'], popular: ['пила']);
        $this->assertStringContainsString('search-list_state_start', $start);
        $this->assertStringContainsString('data-sf-search-recent', $start);
        $this->assertStringContainsString('href="/search?q=%D0%BF%D0%B8%D0%BB%D0%B0"', $start);
        // группа недавних есть и без запросов (её заполняет скрипт из истории), но скрыта
        $this->assertMatchesRegularExpression('/data-sf-search-recent hidden/', SearchList::drawSearchList());

        $this->assertStringContainsString('aria-busy="true"', SearchList::drawSearchList(loading: true, error: true));
        $this->assertStringContainsString('Запрос не найден', SearchList::drawSearchList(error: true));
        $results = SearchList::drawSearchList('тест', products: [['name' => 'Тестер', 'link' => '/p']], categories: [['name' => 'Тесты', 'link' => '/c']]);
        $this->assertStringContainsString('search-list__line', $results);
        $this->assertStringContainsString('href="/search?q=%D1%82%D0%B5%D1%81%D1%82"', $results, '«Показать все» — поиск по запросу');

        $input = SearchInput::drawSearchInput(value: 'дрель', loading: true);
        $this->assertStringContainsString('role="search"', $input);
        $this->assertStringContainsString('aria-busy="true"', $input);
        $this->assertStringContainsString('type="submit"', $input);
        $this->assertStringContainsString('disabled', SearchInput::drawSearchInput(disabled: true));
    }

    public function testSearchMainIsCombobox(): void
    {
        $html = SearchMain::drawSearchMain('s1', ['recent' => ['дрель']], '/qs?q=');
        $this->assertStringContainsString('role="combobox"', $html);
        $this->assertStringContainsString('aria-controls="s1-panel"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertMatchesRegularExpression('/id="s1-panel" data-sf-search-panel hidden/', $html);
        $this->assertStringContainsString('data-sf-search-url="/qs?q="', $html);
        foreach (['loading', 'error', 'query'] as $template) {
            $this->assertStringContainsString('data-sf-search-tpl="' . $template . '"', $html);
        }
        $open = SearchMain::drawSearchMain('s2', open: true);
        $this->assertStringContainsString('search-main_open', $open);
        $this->assertStringContainsString('aria-expanded="true"', $open);
    }

    public function testCategoryCardsLinksAndMore(): void
    {
        $subs = array_map(static fn(int $i): array => ['text' => "Раздел $i", 'link' => "/s/$i"], range(1, 8));
        $card = CardCategory::drawCardCategory('Инструмент', '/c', '10 шт.', label: 'Новинка', subcategories: $subs);
        $this->assertSame(2, substr_count($card, 'data-sf-category-card-extra'), 'седьмая и восьмая — под «Показать все»');
        $this->assertStringContainsString('aria-expanded="false"', $card);
        $this->assertMatchesRegularExpression('/aria-controls="(category-card-[0-9a-f]+)"/', $card);
        $this->assertStringContainsString('class="category-card__title" href="/c"', $card);
        $this->assertStringContainsString('badge-product_new', $card);

        $row = CardCategory::drawCardCategory('Инструмент', '/c', subcategories: $subs, label: 'Хит', style: CardCategoryStyle::Row);
        $this->assertStringNotContainsString('category-card__list', $row);
        $this->assertStringNotContainsString('badge-product', $row);
        $this->assertStringNotContainsString('data-sf-category-card-more', CardCategory::drawCardCategory('А', '/a', subcategories: array_slice($subs, 0, 6)));

        $sub = CardSubcategory::drawCardSubcategory('Дрели', '/d', count: '5 шт.');
        $this->assertStringStartsWith('<a class="subcategory-card', trim($sub));
        $this->assertStringContainsString('5 шт.', $sub);
    }

    public function testNavLevelsAndMenus(): void
    {
        $list = NavCategories::drawNavCategories([['text' => 'А', 'link' => '/a', 'id' => 'x', 'hasChildren' => true, 'current' => true], ['text' => 'Б', 'link' => '/b']]);
        $this->assertStringContainsString('data-sf-nav-item="x"', $list);
        $this->assertStringContainsString('aria-current="page"', $list);
        $this->assertSame(1, substr_count($list, 'data-sf-nav-item='), 'уровень открывают только пункты с подкатегориями');
        $child = NavCategories::drawNavCategories([], NavCategoriesLevel::Child, 'Назад к А');
        $this->assertStringContainsString('data-sf-nav-back', $child);
        $this->assertStringContainsString('nav-categories__empty', $child);
        $this->assertStringContainsString('aria-busy="true"', NavCategories::drawNavCategories([], loading: true));

        $tree = [
            ['text' => 'А', 'link' => '/a', 'children' => [['text' => 'А1', 'link' => '/a1', 'children' => [['text' => 'А11', 'link' => '/a11']]]]],
            ['text' => 'Б', 'link' => '/b', 'children' => [['text' => 'Б1', 'link' => '/b1']]],
            ['text' => 'В', 'link' => '/v'],
        ];
        $mega = NavMega::drawNavMega('m', $tree, promo: ['title' => 'Акция']);
        $this->assertMatchesRegularExpression('/data-sf-nav-panel="c0"(?![^>]*hidden)/', $mega, 'первая категория открыта');
        $this->assertMatchesRegularExpression('/data-sf-nav-panel="c1"[^>]*hidden/', $mega);
        $this->assertStringContainsString('data-sf-nav-panel="c0-0"', $mega);
        $this->assertStringContainsString('nav-mega__promo', $mega);
        $this->assertStringContainsString(' hidden>', $mega, 'закрыто при выводе');

        $catalog = NavCatalog::drawNavCatalog('nc', $tree, searchTarget: 'search');
        $this->assertStringContainsString('role="dialog"', $catalog);
        $this->assertStringContainsString('aria-labelledby="nc-title"', $catalog);
        $this->assertStringContainsString('data-sf-nav-panel="n-0-0" data-sf-nav-parent="n-0"', $catalog);
        $this->assertStringContainsString('data-sf-search-open="search"', $catalog);
        $this->assertStringContainsString('href="/a1"', $catalog, '«Все товары раздела» ведёт в раздел');

        $mobile = NavMobile::drawNavMobile([['text' => 'Акции', 'link' => '/sale']], 'nc', actions: [['text' => 'Избранное', 'link' => '/fav', 'icon' => 'heart', 'badge' => '3']]);
        $this->assertStringContainsString('nav-menu nav-menu-theme_light nav-mobile', $mobile);
        $this->assertStringContainsString('data-sf-nav-catalog-open="nc"', $mobile);
        $this->assertStringContainsString('nav-mobile__actions', $mobile);
        $this->assertLessThan(strpos($mobile, 'nav-mobile__actions'), strpos($mobile, 'nav-mobile__catalog'), '«Каталог» — до списка, кнопки — после');
    }
}
