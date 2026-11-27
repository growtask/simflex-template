<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Base\Geo\GeoList\Layout as GeoList;
use Simflex\Core\Layout\Components\Base\Geo\GeoMain\GeoMainState;
use Simflex\Core\Layout\Components\Base\Geo\GeoMain\Layout as GeoMain;
use Simflex\Core\Layout\Components\Base\Geo\GeoModal\GeoModalStyle;
use Simflex\Core\Layout\Components\Base\Geo\GeoModal\Layout as GeoModal;
use Simflex\Core\Layout\Components\Base\Item\ItemBenefit\Layout as ItemBenefit;
use Simflex\Core\Layout\Components\Base\Player\PlayerAudio\Layout as PlayerAudio;
use Simflex\Core\Layout\Components\Base\Player\PlayerWave\Layout as PlayerWave;
use Simflex\Core\Layout\Components\Base\Slider\SliderMain\Layout as Slider;
use Simflex\Core\Layout\Components\Base\Slider\SliderMain\SliderMainType;
use Simflex\Core\Layout\Components\Base\Slider\SliderStepItem\Layout as SliderStepItem;
use Simflex\Core\Http\PageAssets;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Base/Item, Base/Slider, Base/Geo, Base/Player (страница Base в Figma, этап 3 плана дизайн-системы)
 */
class BaseItemSliderGeoPlayerTest extends Unit
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

    public function testBenefitLineAndList(): void
    {
        $html = ItemBenefit::drawItemBenefit(title: 'Доставка', text: 'Бесплатно', icon: 'truck');
        $this->assertStringContainsString('benefit__head', $html);
        $this->assertStringContainsString('benefit__line', $html);
        $this->assertStringNotContainsString('benefit__line', ItemBenefit::drawItemBenefit(title: 'Без линии', icon: 'truck', line: false));
        $list = ItemBenefit::drawItemBenefitList([['title' => 'А', 'line' => false], ['title' => 'Б'], ['text' => '']]);
        $this->assertSame(2, substr_count($list, '<li'));
        $this->assertSame(1, substr_count($list, 'benefit__line'));
    }

    public function testSliderMarkupAndSwiperAssets(): void
    {
        $cards = ['<div>1</div>', '<div>2</div>', '<div>3</div>'];
        $html = Slider::drawSliderMain($cards, title: 'Популярное', allLink: '/all');
        $this->assertStringContainsString('data-sf-slider', $html);
        $this->assertStringContainsString('class="slider__viewport swiper"', $html);
        $this->assertSame(3, substr_count($html, 'swiper-slide'));
        $this->assertStringContainsString('aria-roledescription="карусель"', $html);
        $this->assertStringContainsString('aria-label="1 из 3"', $html);
        $this->assertStringContainsString('data-sf-slider-prev', $html);
        $this->assertMatchesRegularExpression('/data-sf-slider-prev[^>]*disabled|disabled[^>]*data-sf-slider-prev/', $html);
        $this->assertSame(2, substr_count($html, 'href="/all"'), 'Показать все: вверху и внизу (на телефоне)');
        $this->assertStringContainsString('<h2 class="slider__title">Популярное</h2>', $html);

        $steps = Slider::drawSliderMain($cards, type: SliderMainType::Steps);
        $this->assertSame(3, substr_count($steps, '<span class="step-indicator__item'));
        $none = Slider::drawSliderMain($cards, type: SliderMainType::None, heading: false);
        $this->assertStringNotContainsString('data-sf-slider-next', $none);
        $this->assertStringNotContainsString('slider__title', $none);

        // swiper подключает компонент
        Slider::initAssets();
        $js = implode(' ', array_column((new \ReflectionProperty(PageAssets::class, 'js'))->getValue(), 1));
        $this->assertStringContainsString('swiper-bundle.min.js', $js);

        $item = SliderStepItem::drawSliderStepItem(active: true);
        $this->assertStringContainsString('step-indicator__item step-indicator__item_active', $item);
    }

    public function testGeoStatesAndList(): void
    {
        $cities = ['Москва', 'Мурманск', 'Омск'];
        $static = GeoMain::drawGeoMain(guess: 'Москва', cities: $cities, id: 'g1');
        $this->assertStringContainsString('data-state="static"', $static);
        $this->assertStringContainsString('Москва?', $static);
        $this->assertMatchesRegularExpression('/geo-main__search"\s+hidden/', $static);

        $dropdown = GeoMain::drawGeoMain(cities: $cities, state: GeoMainState::Dropdown, id: 'g2', query: 'Мо');
        $this->assertStringContainsString('value="Мо"', $dropdown);
        $this->assertStringContainsString('aria-controls="g2-list"', $dropdown);
        $this->assertDoesNotMatchRegularExpression('/id="g2-list"[^>]*\shidden/', $dropdown);
        $this->assertStringContainsString('data-city="Москва"', $dropdown);
        $this->assertStringNotContainsString('data-city="Омск"', $dropdown);

        $empty = GeoList::drawGeoList($cities, 'g3', empty: true);
        $this->assertMatchesRegularExpression('/<li\s+data-sf-geo-empty>/', $empty);

        $modal = GeoModal::drawGeoModal(guess: 'Омск', cities: $cities);
        $this->assertStringContainsString('aria-haspopup="dialog"', $modal);
        $this->assertStringContainsString('data-sf-geo-modal', $modal);
        $this->assertStringNotContainsString('data-sf-geo-specimen', $modal);
        $this->assertStringContainsString('data-sf-geo-specimen', GeoModal::drawGeoModal(open: true, style: GeoModalStyle::Mobile));
    }

    public function testPlayerAndWave(): void
    {
        $html = PlayerAudio::drawPlayerAudio('/a.mp3', 'Трек', 'Автор', duration: 3725);
        $this->assertStringContainsString('<audio class="player__audio" src="/a.mp3"', $html);
        $this->assertStringContainsString('aria-label="Аудиоплеер: Трек"', $html);
        $this->assertStringContainsString('role="slider"', $html);
        $this->assertStringContainsString('1:02:05', $html);
        $this->assertStringNotContainsString('player__time', PlayerAudio::drawPlayerAudio('/a.mp3', time: false));

        $wave = PlayerWave::drawPlayerWave(peaks: [0.1, 1], progress: 0.5);
        $this->assertStringContainsString('--wave-progress: 0.5', $wave);
        $this->assertStringContainsString('aria-hidden="true"', $wave);
        $this->assertStringNotContainsString('role="slider"', $wave);
        $this->assertSame(PlayerWave::BARS, substr_count($wave, 'class="player-wave__bar"'));
        $this->assertSame(PlayerWave::BARS, substr_count($wave, 'player-wave__bar_played'));
    }
}
