<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Parts\System\SystemAgeGate\SystemAgeGateTheme;
use Simflex\Core\Layout\Components\Parts\System\SystemAgeGate\Layout as AgeGate;
use Simflex\Core\Layout\Components\Parts\System\SystemBrowserSupport\SystemBrowserSupportTheme;
use Simflex\Core\Layout\Components\Parts\System\SystemBrowserSupport\Layout as BrowserSupport;
use Simflex\Core\Layout\Components\Parts\System\SystemConsent\SystemConsentTheme;
use Simflex\Core\Layout\Components\Parts\System\SystemConsent\Layout as Consent;
use Simflex\Core\Layout\Components\Parts\System\SystemCookie\SystemCookiePosition;
use Simflex\Core\Layout\Components\Parts\System\SystemCookie\SystemCookieState;
use Simflex\Core\Layout\Components\Parts\System\SystemCookie\SystemCookieTheme;
use Simflex\Core\Layout\Components\Parts\System\SystemCookie\Layout as Cookie;
use Simflex\Core\Layout\Components\Parts\System\SystemMaintenance\Layout as Maintenance;
use Simflex\Core\Layout\Components\Parts\System\SystemMaintenance\SystemMaintenanceTheme;
use Simflex\Core\Layout\Components\Parts\System\SystemPreloader\Layout as Preloader;
use Simflex\Core\Layout\Components\Parts\System\SystemPreloader\SystemPreloaderTheme;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Parts/System (блок System в Figma): cookie, consent, age-gate, browser-support, preloader, maintenance
 */
class PartsSystemTest extends Unit
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

    public function testCookieHasBothViewsAndActions(): void
    {
        $html = Cookie::drawSystemCookie();
        $this->assertStringContainsString('cookie-state_default', $html);
        $this->assertStringContainsString('cookie-position_bottom', $html);
        $this->assertStringContainsString('data-sf-cookie', $html);
        foreach (['accept', 'settings', 'decline', 'save'] as $action) {
            $this->assertStringContainsString('data-sf-cookie-action="' . $action . '"', $html);
        }
        // три категории, необходимая включена и недоступна для выключения
        foreach (['necessary', 'analytics', 'marketing'] as $key) {
            $this->assertStringContainsString('name="cookie-' . $key . '"', $html);
        }
        $this->assertSame(1, substr_count($html, 'aria-disabled="true"'));
        $this->assertStringContainsString('политике обработки персональных данных', $html);
        $this->assertStringNotContainsString('cookie_inline', $html);
    }

    public function testCookieOptions(): void
    {
        $settings = Cookie::drawSystemCookie(
            state: SystemCookieState::Settings,
            theme: SystemCookieTheme::Dark,
            position: SystemCookiePosition::Top,
            policyUrl: '',
            inline: true,
        );
        $this->assertStringContainsString('cookie-state_settings', $settings);
        $this->assertStringContainsString('cookie-theme_dark', $settings);
        $this->assertStringContainsString('cookie-position_top', $settings);
        $this->assertStringContainsString('cookie_inline', $settings);
        $this->assertStringNotContainsString('cookie__more', $settings);
        $this->assertStringContainsString('btn-theme_dark', $settings);
        $escaped = Cookie::drawSystemCookie(title: 'A <b>', categories: [['key' => 'x', 'text' => '<i>']]);
        $this->assertStringContainsString('A &lt;b&gt;', $escaped);
        $this->assertStringNotContainsString('<i>', $escaped);
    }

    public function testConsentIsRequiredCheckboxWithPolicyLink(): void
    {
        $html = Consent::drawSystemConsent(policyUrl: '/privacy');
        $this->assertStringContainsString('consent-theme_light', $html);
        $this->assertStringContainsString('name="consent"', $html);
        $this->assertStringContainsString('required', $html);
        $this->assertStringContainsString('href="/privacy"', $html);
        $this->assertStringContainsString('Я принимаю', $html);
        $this->assertStringNotContainsString('checkbox_error', $html);
        $error = Consent::drawSystemConsent(theme: SystemConsentTheme::Dark, error: true, attributes: ['name' => 'agree']);
        $this->assertStringContainsString('checkbox_error', $error);
        $this->assertStringContainsString('name="agree"', $error);
        $this->assertStringContainsString('consent-theme_dark', $error);
    }

    public function testAgeGateDialogAndActions(): void
    {
        $html = AgeGate::drawSystemAgeGate();
        $this->assertStringContainsString('data-sf-age-gate', $html);
        $this->assertStringContainsString('role="dialog"', $html);
        $this->assertStringContainsString('aria-modal="true"', $html);
        $this->assertStringContainsString('Вам есть 18 лет?', $html);
        $this->assertStringContainsString('data-sf-age-gate-action="confirm"', $html);
        $this->assertStringContainsString('data-sf-age-gate-action="decline"', $html);
        // в рамке витрины скрипт окно не трогает
        $inline = AgeGate::drawSystemAgeGate(theme: SystemAgeGateTheme::Dark, inline: true, logoHtml: '<i class="logo-x"></i>');
        $this->assertStringNotContainsString('data-sf-age-gate ', $inline);
        $this->assertStringNotContainsString('data-sf-age-gate>', $inline);
        $this->assertStringContainsString('age-gate_inline', $inline);
        $this->assertStringContainsString('logo-x', $inline);
    }

    public function testBrowserSupportCheckHidesBanner(): void
    {
        $checked = BrowserSupport::drawSystemBrowserSupport(buttonLink: 'https://example.com');
        $this->assertStringContainsString('hidden="hidden"', $checked);
        $this->assertStringContainsString('data-sf-browser-support', $checked);
        $this->assertStringContainsString('href="https://example.com"', $checked);
        $this->assertStringContainsString('icon-triangle-alert', $checked);
        $always = BrowserSupport::drawSystemBrowserSupport(theme: SystemBrowserSupportTheme::Dark, check: false);
        $this->assertStringNotContainsString('hidden="hidden"', $always);
        $this->assertStringNotContainsString('data-sf-browser-support', $always);
        // без ссылки кнопки нет
        $this->assertStringNotContainsString('browser-support__action', $always);
        $this->assertStringContainsString('browser-support-theme_dark', $always);
    }

    public function testPreloaderHasLoaderAndMinDuration(): void
    {
        $html = Preloader::drawSystemPreloader(minDuration: 800, label: 'Ждём');
        $this->assertStringContainsString('data-sf-preloader="800"', $html);
        $this->assertStringContainsString('aria-busy="true"', $html);
        $this->assertStringContainsString('loader_size_xl', $html);
        $this->assertStringContainsString('Ждём', $html);
        $inline = Preloader::drawSystemPreloader(theme: SystemPreloaderTheme::Dark, inline: true);
        $this->assertStringNotContainsString('data-sf-preloader', $inline);
        $this->assertStringContainsString('preloader_inline', $inline);
        $this->assertStringContainsString('loader-theme_dark', $inline);
        $this->assertStringContainsString('data-sf-preloader="0"', Preloader::drawSystemPreloader(minDuration: -5));
    }

    public function testMaintenanceTextsButtonAndTag(): void
    {
        $html = Maintenance::drawSystemMaintenance(buttonLink: 'mailto:a@b.ru', title: 'Работы <b>');
        $this->assertStringContainsString('<main class="maintenance', $html);
        $this->assertStringContainsString('Работы &lt;b&gt;', $html);
        $this->assertStringContainsString('href="mailto:a@b.ru"', $html);
        $this->assertStringContainsString('Связаться с нами', $html);
        $inline = Maintenance::drawSystemMaintenance(theme: SystemMaintenanceTheme::Dark, inline: true);
        $this->assertStringContainsString('<div class="maintenance', $inline);
        $this->assertStringContainsString('maintenance_inline', $inline);
        $this->assertStringContainsString('maintenance-theme_dark', $inline);
        // без ссылки кнопки нет
        $this->assertStringNotContainsString('maintenance__action', $inline);
    }
}
