<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Parts\Brand\BrandLogo\Layout as BrandLogo;
use Simflex\Core\Layout\Components\Parts\Brand\BrandLogo\LogoTheme;
use Simflex\Core\Layout\Components\Parts\Brand\BrandLogoAnim\Layout as BrandLogoAnim;
use Simflex\Core\Layout\Components\Parts\Brand\BrandLogoAnim\LogoAnimTheme;
use Simflex\Core\Layout\Components\Parts\Brand\BrandMadeBy\Layout as BrandMadeBy;
use Simflex\Core\Layout\Components\Parts\Brand\BrandMadeBy\MadeByTheme;
use Simflex\Core\Layout\Components\Parts\Brand\BrandMadeIn\Layout as BrandMadeIn;
use Simflex\Core\Layout\Components\Parts\Brand\BrandMadeIn\MadeInTheme;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Parts/Brand (блок Brand в Figma): logo-anim, logo, made-in, made-by своя разметка и стили
 */
class PartsBrandTest extends Unit
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

    public function testBrandLogoIsStaticLogo(): void
    {
        $html = BrandLogo::drawBrandLogo(name: 'Simflex');
        $this->assertStringContainsString('brand-logo', $html);
        $this->assertStringContainsString('brand-logo-theme_light', $html);
        $this->assertStringContainsString('href="/"', $html);
        $this->assertStringContainsString('images/brand/logo.svg', $html);
        $this->assertStringContainsString('alt="Simflex"', $html);
        $dark = BrandLogo::drawBrandLogo(theme: LogoTheme::Dark, href: '/about', src: '/assets/default/images/brand/made-in.svg');
        $this->assertStringContainsString('brand-logo-theme_dark', $dark);
        $this->assertStringContainsString('href="/about"', $dark);
        $this->assertStringContainsString('src="/assets/default/images/brand/made-in.svg"', $dark);
    }

    public function testBrandLogoAnimHasHomeTextAndCaption(): void
    {
        $html = BrandLogoAnim::drawBrandLogoAnim(name: 'Simflex', caption: 'Shop');
        $this->assertStringContainsString('brand-logo-anim', $html);
        $this->assertStringContainsString('brand-logo-anim__home', $html);
        $this->assertStringContainsString('Главная', $html);
        $this->assertStringContainsString('brand-logo-anim__caption', $html);
        $this->assertStringContainsString('Shop', $html);
        // text выключает разделитель и подпись
        $plain = BrandLogoAnim::drawBrandLogoAnim(theme: LogoAnimTheme::Dark, caption: 'Shop', text: false, homeText: 'Домой');
        $this->assertStringNotContainsString('brand-logo-anim__caption', $plain);
        $this->assertStringContainsString('Домой', $plain);
        $this->assertStringContainsString('brand-logo-anim-theme_dark', $plain);
        $this->assertStringContainsString('images/brand/logo-dark.svg', $plain);
    }

    public function testBrandMadeInFlagAndExternalLink(): void
    {
        $html = BrandMadeIn::drawBrandMadeIn(name: 'Гротеск', href: 'https://example.com');
        $this->assertStringContainsString('brand-made-in', $html);
        $this->assertStringContainsString('brand-made-in__flag', $html);
        $this->assertStringContainsString('Сайт сделан в студии', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $this->assertStringContainsString('rel="noopener"', $html);
        $noFlag = BrandMadeIn::drawBrandMadeIn(theme: MadeInTheme::Dark, flag: false, caption: 'Разработка');
        $this->assertStringNotContainsString('brand-made-in__flag', $noFlag);
        $this->assertStringContainsString('Разработка', $noFlag);
        $this->assertStringContainsString('images/brand/made-in-dark.svg', $noFlag);
    }

    public function testBrandMadeByPoweredByAndTheme(): void
    {
        $html = BrandMadeBy::drawBrandMadeBy(name: 'Симфлекс', href: 'https://example.com');
        $this->assertStringContainsString('brand-made-by', $html);
        $this->assertStringContainsString('Powered by', $html);
        $this->assertStringContainsString('images/brand/made-by.svg', $html);
        $this->assertStringContainsString('target="_blank"', $html);
        $dark = BrandMadeBy::drawBrandMadeBy(theme: MadeByTheme::Dark);
        $this->assertStringContainsString('images/brand/made-by-dark.svg', $dark);
        $this->assertStringContainsString('brand-made-by-theme_dark', $dark);
    }
}
