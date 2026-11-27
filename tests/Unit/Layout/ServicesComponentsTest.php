<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Services\Card\CardMain\CardMainLayout;
use Simflex\Core\Layout\Components\Services\Card\CardMain\Layout as Card;
use Simflex\Core\Layout\Components\Services\Hero\HeroGallery\Layout as Gallery;
use Simflex\Core\Layout\Components\Services\Hero\HeroMain\HeroMainLayout;
use Simflex\Core\Layout\Components\Services\Hero\HeroMain\Layout as Hero;
use Simflex\Core\Layout\Components\Services\Price\PriceMain\Layout as Price;
use Simflex\Core\Layout\Components\Services\Price\PriceMain\PriceMainStyle;
use Simflex\Core\Layout\Components\Services\Price\PriceMain\PriceMainType;
use Simflex\Core\Layout\Components\Services\Step\StepMain\Layout as Steps;
use Simflex\Core\Layout\Components\Services\Step\StepMain\StepMainStyle;
use Simflex\Core\Layout\Components\Services\Tariff\TariffList\Layout as TariffList;
use Simflex\Core\Layout\Components\Services\Tariff\TariffList\TariffListPeriod;
use Simflex\Core\Layout\Components\Services\Tariff\TariffMain\Layout as Tariff;
use Simflex\Core\Layout\Components\Services\Tariff\TariffTable\Layout as TariffTable;
use Simflex\Core\Layout\Components\Services\Tariff\TariffTable\TariffTablePlan;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Компоненты страницы Services в Figma (этап 4 плана дизайн-системы): разметка, доступность, состав по параметрам
 */
class ServicesComponentsTest extends Unit
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

    public function testCardIsOneLinkWithSeparateOrder(): void
    {
        $html = Card::drawCardMain('Сайт', '/services/site', price: '10 000 ₽', oldPrice: '12 000 ₽', time: '10 часов', label: 'Хит');
        $this->assertSame(1, substr_count($html, '<a '), 'одна ссылка — заголовок, растянутый на карточку');
        $this->assertStringContainsString('class="service-card__title" href="/services/site"', $html);
        $this->assertStringContainsString('service-card__action', $html);
        $this->assertStringContainsString('#icon-banknote', $html);
        $this->assertStringContainsString('#icon-clock', $html);
        $this->assertStringContainsString('Старая цена: </span>12 000 ₽', $html);
        $this->assertStringContainsString('service-card_layout_list', Card::drawCardMain('Сайт', '/s', layout: CardMainLayout::List));

        $disabled = Card::drawCardMain('Сайт', '/s', disabled: true);
        $this->assertStringContainsString('aria-disabled="true"', $disabled);
        $this->assertStringNotContainsString('<a ', $disabled);
        $this->assertStringNotContainsString('service-card__action', $disabled);
    }

    public function testPriceStylesAndUnavailable(): void
    {
        $html = Price::drawPriceMain('10 000 ₽', from: 'от', unit: '/ проект', oldPrice: '12 000 ₽', discount: '-17%', time: '10 часов');
        foreach (['price__from', 'price__unit', 'price__old', 'price__discount', 'price__time', 'Заказать', 'Спросить'] as $part) {
            $this->assertStringContainsString($part, $html);
        }
        $this->assertStringNotContainsString('price__action', Price::drawPriceMain('1 ₽', style: PriceMainStyle::Compact));
        $this->assertStringContainsString('По запросу', Price::drawPriceMain(type: PriceMainType::Special));
        $unavailable = Price::drawPriceMain('1 ₽', unavailable: true);
        $this->assertStringContainsString('Недоступно', $unavailable);
        $this->assertStringNotContainsString('Заказать', $unavailable);

        $sticky = Price::drawPriceMain('1 ₽', style: PriceMainStyle::Sticky, watch: 'price-main');
        $this->assertStringContainsString('data-sf-price-sticky="price-main"', $sticky);
        $this->assertStringContainsString('aria-hidden="true"', $sticky);
        $this->assertStringNotContainsString('Спросить', $sticky);
        $this->assertStringNotContainsString('data-sf-price-sticky', Price::drawPriceMain('1 ₽', style: PriceMainStyle::Sticky, inline: true));
    }

    public function testTariffFeaturesAndPeriods(): void
    {
        $html = Tariff::drawTariffMain('Стандарт', price: '10 000 ₽', period: '/ мес', year: ['price' => '96 000 ₽'],
            features: [['text' => 'А', 'hint' => 'Подсказка'], ['text' => 'Б', 'included' => false]], featured: true);
        $this->assertStringContainsString('tariff_featured', $html);
        $this->assertStringContainsString('btn-primary', $html);
        $this->assertStringContainsString('data-tariff-period="month"', $html);
        $this->assertStringContainsString('data-tariff-period="year"', $html);
        $this->assertStringContainsString('Входит: </span>А', $html);
        $this->assertStringContainsString('Не входит: </span>Б', $html);
        $this->assertStringContainsString('tooltip-info', $html);
        $this->assertStringContainsString('btn-gray', Tariff::drawTariffMain('Базовый', price: '1 ₽'));

        $list = TariffList::drawTariffList([['name' => 'А', 'price' => '1 ₽'], ['name' => 'Б', 'price' => '2 ₽']], TariffListPeriod::Year);
        $this->assertStringContainsString('tariff-list_period_year', $list);
        $this->assertSame(2, substr_count($list, 'swiper-slide'));
        $this->assertStringContainsString('data-value="year"', $list);
    }

    public function testTariffTableIsAccessibleTable(): void
    {
        $html = TariffTable::drawTariffTable(
            [['name' => 'А', 'price' => '1 ₽'], ['name' => 'Б', 'featured' => true], ['name' => 'В'], ['name' => 'Лишний']],
            [['text' => 'Функция', 'values' => [true, false, true]]],
            plan: TariffTablePlan::Standard,
        );
        $this->assertStringContainsString('<table', $html);
        $this->assertSame(4, substr_count($html, 'scope="col"'), 'возможности и три тарифа: четвёртый отброшен');
        $this->assertStringContainsString('scope="row"', $html);
        $this->assertStringContainsString('tariff-table_plan_1', $html);
        $this->assertStringContainsString('tariff-table__col_featured', $html);
        $this->assertSame(2, substr_count($html, '>входит<'));
        $this->assertSame(1, substr_count($html, '>не входит<'));
    }

    public function testStepsProgressAndLines(): void
    {
        $steps = [['title' => 'Заявка', 'done' => true], ['title' => 'Оценка'], ['title' => 'Работа']];
        $html = Steps::drawStepMain($steps, progress: true, actionText: 'Заказать');
        $this->assertStringContainsString('<ol class="steps__list">', $html);
        $this->assertStringContainsString('Пройдено: Заявка', $html);
        $this->assertStringContainsString('#icon-check', $html);
        $this->assertStringContainsString('>02<', $html);
        $this->assertSame(2, substr_count($html, 'benefit__line'), 'у последнего шага линии нет');
        $this->assertStringNotContainsString('Пройдено', Steps::drawStepMain($steps));

        $scroll = Steps::drawStepMain($steps, style: StepMainStyle::Scroll);
        $this->assertStringContainsString('data-sf-slider', $scroll);
        $this->assertStringNotContainsString('benefit__line', $scroll);
    }

    public function testHeroLayoutsAndGallery(): void
    {
        $hero = Hero::drawHeroMain('Сайты', badges: ['Хит', 'Новое'], benefits: [['title' => 'Быстро']],
            price: ['price' => '1 ₽'], portfolioLink: '/portfolio', image: '/assets/default/images/banner/main.webp');
        $this->assertStringContainsString('<h1 class="hero__title">Сайты</h1>', $hero);
        $this->assertStringContainsString('hero__media', $hero);
        $this->assertStringContainsString('class="price ', $hero);
        $this->assertStringContainsString('href="/portfolio"', $hero);
        $this->assertStringContainsString('badge-primary-light', $hero);
        $this->assertStringContainsString('badge-secondary', $hero);

        $cover = Hero::drawHeroMain('Сайты', price: ['price' => '1 ₽'], image: '/assets/default/images/banner/main.webp', layout: HeroMainLayout::Cover);
        $this->assertStringContainsString('hero__cover', $cover);
        $this->assertStringContainsString('price-theme_dark', $cover, 'на подложке cover — тёмная тема');
        $this->assertStringNotContainsString('hero__media', Hero::drawHeroMain('Сайты', image: '/x.webp', layout: HeroMainLayout::Center));

        $gallery = Gallery::drawHeroGallery([['src' => '/a.webp'], ['src' => '/b.webp'], ['src' => '/c.webp']], active: 2);
        $this->assertSame(3, substr_count($gallery, 'data-sf-hero-gallery-thumb='));
        $this->assertStringContainsString('data-active="1"', $gallery);
        $this->assertMatchesRegularExpression('/aria-pressed="true"\s+data-sf-hero-gallery-thumb="1"/', $gallery);
    }
}
