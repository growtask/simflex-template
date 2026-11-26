<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Parts\Placeholder\PlaceholderImg\PlaceholderImgMissing;
use Simflex\Core\Layout\Components\Parts\Placeholder\PlaceholderImg\Layout as PlaceholderImg;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Parts/Placeholder/PlaceholderImg: все картинки компонентов и сайта выводятся через него
 */
class PlaceholderImgTest extends Unit
{
    private const LOGO = '/assets/default/images/brand/logo.svg';

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

    public function testExists(): void
    {
        $this->assertTrue(PlaceholderImg::exists(self::LOGO));
        $this->assertTrue(PlaceholderImg::exists(self::LOGO . '?v=123'));
        $this->assertTrue(PlaceholderImg::exists('https://example.com/a.jpg'));
        $this->assertTrue(PlaceholderImg::exists('//cdn.example.com/a.jpg'));
        $this->assertFalse(PlaceholderImg::exists(''));
        $this->assertFalse(PlaceholderImg::exists('/uf/no-such-file.jpg'));
        // не картинка и выход из public/
        $this->assertFalse(PlaceholderImg::exists('/index.php'));
        $this->assertFalse(PlaceholderImg::exists('/assets/../../composer.json'));
    }

    public function testDrawsPictureWithDefaults(): void
    {
        $html = PlaceholderImg::drawPlaceholderImg(src: self::LOGO, alt: 'Лого <1>', width: 100, height: 40, imgClass: 'logo__img');
        $this->assertStringContainsString('<picture class="placeholder-img">', $html);
        $this->assertStringContainsString('src="' . self::LOGO . '"', $html);
        $this->assertStringContainsString('alt="Лого &lt;1&gt;"', $html);
        $this->assertStringContainsString('placeholder-img__image logo__img', $html);
        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('decoding="async"', $html);
        $this->assertStringContainsString('width="100"', $html);
        $this->assertStringContainsString('height="40"', $html);
        // одна картинка - source не нужен
        $this->assertStringNotContainsString('<source', $html);
        // значения вызывающего кода сильнее умолчаний
        $eager = PlaceholderImg::drawPlaceholderImg(src: self::LOGO, attributes: ['loading' => 'eager', 'fetchpriority' => 'high']);
        $this->assertStringContainsString('loading="eager"', $eager);
        $this->assertStringNotContainsString('loading="lazy"', $eager);
        $this->assertStringContainsString('fetchpriority="high"', $eager);
    }

    public function testMobileSource(): void
    {
        $mobile = '/assets/default/images/brand/logo-dark.svg';
        $html = PlaceholderImg::drawPlaceholderImg(src: self::LOGO, srcMobile: $mobile, size: 600);
        $this->assertStringContainsString('media="(max-width: 600px)"', $html);
        $this->assertStringContainsString('media="(min-width: 601px)"', $html);
        $this->assertStringContainsString('srcset="' . $mobile . '"', $html);
        // нет картинки для телефона - одна на оба размера, без source
        $this->assertStringNotContainsString('<source', PlaceholderImg::drawPlaceholderImg(src: self::LOGO, srcMobile: '/uf/none.jpg'));
        // нет картинки для широких экранов - берётся мобильная
        $only = PlaceholderImg::drawPlaceholderImg(src: '/uf/none.jpg', srcMobile: self::LOGO);
        $this->assertStringContainsString('src="' . self::LOGO . '"', $only);
        $this->assertStringNotContainsString('placeholder-img_missing', $only);
    }

    public function testMissingFileShowsPlaceholderOrNothing(): void
    {
        $html = PlaceholderImg::drawPlaceholderImg(src: '/uf/no-such-file.jpg', alt: 'Нет');
        $this->assertStringContainsString('placeholder-img_missing', $html);
        $this->assertStringContainsString('images/placeholders/fallback-img.webp', $html);
        $this->assertStringNotContainsString('no-such-file', $html);

        $custom = PlaceholderImg::drawPlaceholderImg(src: '', placeholder: self::LOGO);
        $this->assertStringContainsString('src="' . self::LOGO . '"', $custom);

        $this->assertSame('', PlaceholderImg::drawPlaceholderImg(src: '/uf/no-such-file.jpg', missing: PlaceholderImgMissing::Hide));
        // в теме без заглушки тоже ничего
        Container::set('config', (object)['theme' => 'no-such-theme', 'devMode' => true]);
        $this->assertSame('', PlaceholderImg::drawPlaceholderImg(src: '/uf/no-such-file.jpg'));
    }

    public function testDeferredKeepsFileInData(): void
    {
        $html = PlaceholderImg::drawPlaceholderImg(src: self::LOGO, srcMobile: '/assets/default/images/brand/logo-dark.svg', deferred: true);
        $this->assertStringContainsString('data-srcset="' . self::LOGO . '"', $html);
        $this->assertStringNotContainsString(' srcset=', $html);
        $this->assertStringContainsString('data-src="' . self::LOGO . '"', $html);
        $this->assertStringContainsString('src="data:image/gif;base64,', $html);
        // у недостающей картинки откладывать нечего
        $missing = PlaceholderImg::drawPlaceholderImg(src: '/uf/none.jpg', deferred: true);
        $this->assertStringNotContainsString('data-src', $missing);
    }
}
