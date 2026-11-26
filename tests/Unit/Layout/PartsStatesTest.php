<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Parts\States\Layout as States;
use Simflex\Core\Layout\Components\Parts\States\StatesStyle;
use Simflex\Core\Layout\Components\Parts\States\StatesTheme;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Parts/States (блок States в Figma): состояния empty, load, alert, success, error
 */
class PartsStatesTest extends Unit
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

    public function testEveryStyleHasMarkerTextsAndButtons(): void
    {
        $markers = [
            'empty' => 'marker-main', 'load' => 'marker-load', 'alert' => 'marker-alert',
            'success' => 'marker-success', 'error' => 'marker-error',
        ];
        foreach (StatesStyle::cases() as $style) {
            $html = States::drawStates(style: $style);
            $this->assertStringContainsString('states states-' . $style->value, $html);
            $this->assertStringContainsString($markers[$style->value], $html);
            $this->assertStringContainsString('marker-size_xl', $html);
            $this->assertStringContainsString($style->getTitle(), $html);
            $this->assertStringContainsString($style->getText(), $html);
            $this->assertStringContainsString('states-theme_light', $html);
        }
    }

    public function testButtonsFollowFigma(): void
    {
        // у load кнопок нет, у остальных две: светлая (secondary) и основная
        $this->assertStringNotContainsString('states__buttons', States::drawStates(style: StatesStyle::Load));
        $error = States::drawStates(style: StatesStyle::Error);
        $this->assertSame(2, preg_match_all('/states__button(?!s)/', $error));
        $this->assertStringContainsString('Повторить', $error);
        $this->assertStringContainsString('Главная', $error);
        $this->assertStringContainsString('Отмена', States::drawStates(style: StatesStyle::Alert));
        // buttons включает кнопки у load, secondaryButton убирает вторую
        $this->assertStringContainsString('states__buttons', States::drawStates(style: StatesStyle::Load, buttons: true));
        $single = States::drawStates(style: StatesStyle::Success, secondaryButton: false);
        $this->assertSame(1, preg_match_all('/states__button(?!s)/', $single));
        $this->assertStringNotContainsString('states__buttons', States::drawStates(style: StatesStyle::Empty, buttons: false));
    }

    public function testCustomTextsLinksAndEscaping(): void
    {
        $html = States::drawStates(
            title: 'Пусто <b>',
            text: 'Нет данных',
            primaryText: 'В каталог',
            primaryLink: '/catalog',
            secondaryText: 'Домой',
            secondaryLink: '/',
        );
        $this->assertStringContainsString('Пусто &lt;b&gt;', $html);
        $this->assertStringNotContainsString('Пусто <b>', $html);
        $this->assertStringContainsString('href="/catalog"', $html);
        $this->assertStringContainsString('href="/"', $html);
        $this->assertStringContainsString('В каталог', $html);
        $this->assertStringContainsString('Домой', $html);
    }

    public function testRolesAndLoadingFlag(): void
    {
        $this->assertStringContainsString('role="alert"', States::drawStates(style: StatesStyle::Error));
        $this->assertStringContainsString('role="alert"', States::drawStates(style: StatesStyle::Alert));
        $this->assertStringContainsString('role="status"', States::drawStates(style: StatesStyle::Success));
        $load = States::drawStates(style: StatesStyle::Load);
        $this->assertStringContainsString('role="status"', $load);
        $this->assertStringContainsString('aria-busy="true"', $load);
        $this->assertStringNotContainsString('role=', States::drawStates(style: StatesStyle::Empty));
    }

    public function testThemeAndTitleTag(): void
    {
        $dark = States::drawStates(theme: StatesTheme::Dark);
        $this->assertStringContainsString('states-theme_dark', $dark);
        $this->assertStringContainsString('marker-theme_dark', $dark);
        $this->assertStringContainsString('btn-theme_dark', $dark);
        $this->assertStringContainsString('<h2 class="states__title">', $dark);
        $this->assertStringContainsString('<h1 class="states__title">', States::drawStates(titleTag: 'h1'));
        // неизвестный тег заменяется на h2
        $this->assertStringContainsString('<h2 class="states__title">', States::drawStates(titleTag: 'script'));
    }
}
