<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Common\Banner\BannerTheme;
use Simflex\Core\Layout\Components\Common\Banner\Layout as Banner;
use Simflex\Core\Layout\Components\Common\Benefit\BenefitAlign;
use Simflex\Core\Layout\Components\Common\Benefit\BenefitLayout;
use Simflex\Core\Layout\Components\Common\Benefit\BenefitSize;
use Simflex\Core\Layout\Components\Common\Benefit\BenefitType;
use Simflex\Core\Layout\Components\Common\Benefit\Layout as Benefit;
use Simflex\Core\System\Container;

/**
 * Отрисовка Common/Banner и Common/Benefit: слайды, доступность, размеры, enum
 */
class BannerBenefitTest extends Unit
{
    private mixed $config = null;

    protected function _before(): void
    {
        // Иконки берут тему из конфига сайта
        $this->config = Container::get('config');
        Container::set('config', (object)['theme' => 'default']);
    }

    protected function _after(): void
    {
        Container::set('config', $this->config);
    }

    private function slides(int $count): array
    {
        $slides = [];
        for ($i = 1; $i <= $count; $i++) {
            $slides[] = [
                'title' => "Слайд $i <b>",
                'text' => "Текст $i",
                'image' => "/img/$i.jpg",
                'alt' => "Картинка $i",
                'buttonText' => 'Подробнее',
                'buttonLink' => "/page-$i",
            ];
        }

        return $slides;
    }

    public function testBannerEmptyWithoutSlides(): void
    {
        $this->assertSame('', Banner::drawBanner(slides: []));
        $this->assertSame('', Banner::drawBanner(slides: [['text' => 'без заголовка и картинки']]));
    }

    public function testBannerSingleSlideHasNoControls(): void
    {
        $html = Banner::drawBanner(slides: $this->slides(1), label: 'Главный баннер');

        $this->assertStringContainsString('aria-roledescription="carousel"', $html);
        $this->assertStringContainsString('aria-label="Главный баннер"', $html);
        $this->assertStringNotContainsString('data-banner', $html);
        $this->assertStringNotContainsString('banner__arrows', $html);
        $this->assertStringNotContainsString('step-indicator', $html);
        $this->assertStringContainsString('loading="eager"', $html);
        $this->assertStringContainsString('<h1 class="banner__title">', $html);
    }

    public function testBannerSlidesArrowsAndSteps(): void
    {
        $html = Banner::drawBanner(
            slides: $this->slides(3),
            className: 'extra',
            autoplay: false,
            interval: 4000,
            headingLevel: 2,
            theme: BannerTheme::Dark
        );

        $this->assertStringContainsString('banner-theme_dark', $html);
        $this->assertStringContainsString('extra', $html);
        $this->assertStringContainsString('data-autoplay="0"', $html);
        $this->assertStringContainsString('data-interval="4000"', $html);
        $this->assertSame(3, substr_count($html, 'class="banner__slide"'));
        $this->assertStringContainsString('aria-label="2 из 3"', $html);
        // Первая картинка сразу, остальные лениво
        $this->assertSame(1, substr_count($html, 'loading="eager"'));
        $this->assertSame(2, substr_count($html, 'loading="lazy"'));
        // Неактивные слайды скрыты от скринридеров
        $this->assertSame(2, substr_count($html, 'inert=""'));
        // Стрелка «назад» на первом слайде отключена, имена стрелок заданы
        $this->assertStringContainsString('data-banner-prev', $html);
        $this->assertStringContainsString('aria-label="Назад"', $html);
        $this->assertStringContainsString('aria-label="Вперёд"', $html);
        $this->assertMatchesRegularExpression('/<button[^>]*disabled[^>]*data-banner-prev|<button[^>]*data-banner-prev[^>]*disabled/s', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertSame(3, substr_count($html, 'class="step-indicator__item'));
        // Экранирование
        $this->assertStringContainsString('Слайд 1 &lt;b&gt;', $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringContainsString('<h2 class="banner__title">', $html);
    }

    public function testBannerSkipsButtonWithoutLink(): void
    {
        $html = Banner::drawBanner(slides: [['title' => 'Один', 'buttonText' => 'Кнопка']]);

        $this->assertStringNotContainsString('banner__button', $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function testBenefitClassesAndMarker(): void
    {
        $html = Benefit::drawBenefit(
            title: 'Доставка <i>',
            text: 'Быстро',
            icon: 'clock',
            size: BenefitSize::Large,
            layout: BenefitLayout::Horizontal,
            align: BenefitAlign::Center,
            card: true,
            className: 'extra'
        );

        $this->assertStringStartsWith('<li', ltrim($html));
        $this->assertStringContainsString('benefit-size_l', $html);
        $this->assertStringContainsString('benefit-type_icon', $html);
        $this->assertStringContainsString('benefit-layout_horizontal', $html);
        // У horizontal выравнивание всегда по левому краю
        $this->assertStringContainsString('benefit-align_left', $html);
        $this->assertStringNotContainsString('benefit-align_center', $html);
        $this->assertStringContainsString('benefit-card', $html);
        $this->assertStringContainsString('extra', $html);
        $this->assertStringContainsString('marker-size_l', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
        $this->assertStringContainsString('<h3 class="benefit__title">Доставка &lt;i&gt;</h3>', $html);
    }

    public function testBenefitNumberLinkAndBadge(): void
    {
        $html = Benefit::drawBenefit(
            title: 'Шаг',
            text: 'Текст',
            number: '01',
            type: BenefitType::Number,
            size: BenefitSize::Small,
            link: ['text' => 'Подробнее', 'href' => '/about'],
            badge: '24/7',
            headingLevel: 4
        );

        $this->assertStringContainsString('benefit__number">01<', $html);
        $this->assertStringNotContainsString('marker', $html);
        $this->assertStringContainsString('<h4 class="benefit__title">', $html);
        $this->assertStringContainsString('href="/about"', $html);
        $this->assertStringContainsString('aria-label="Подробнее: Шаг"', $html);
        $this->assertStringContainsString('link-size_s', $html);
        $this->assertStringContainsString('24/7', $html);
        $this->assertStringContainsString('badge-size_xs', $html);
    }

    public function testBenefitIgnoresLinkWithoutHref(): void
    {
        $html = Benefit::drawBenefit(title: 'A', link: ['text' => 'Подробнее', 'href' => '']);

        $this->assertStringNotContainsString('benefit__link', $html);
    }

    public function testBenefitList(): void
    {
        $html = Benefit::drawBenefitList(
            items: [
                ['title' => 'Один', 'icon' => 'clock'],
                ['icon' => 'file'],
                ['title' => 'Два', 'text' => 'Текст'],
            ],
            className: 'list-extra',
            itemClassName: 'item-extra'
        );

        $this->assertStringStartsWith('<ul class="benefit-list list-extra"', $html);
        $this->assertSame(2, substr_count($html, '<li'));
        $this->assertSame(2, substr_count($html, 'item-extra'));
        $this->assertSame('', Benefit::drawBenefitList(items: []));
    }
}
