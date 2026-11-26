<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\System\Container;
use Simflex\Core\Layout\Components\UI\Badge\BadgeSize;
use Simflex\Core\Layout\Components\UI\Badge\BadgeStyle;
use Simflex\Core\Layout\Components\UI\Badge\Layout as Badge;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\ButtonMainIconPos;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\ButtonMainSize;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\ButtonMainStyle;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\Layout as Button;
use Simflex\Core\Layout\Components\UI\Switch\SwitchCheckbox\SwitchCheckboxSize;
use Simflex\Core\Layout\Components\UI\Switch\SwitchCheckbox\Layout as Checkbox;
use Simflex\Core\Layout\Components\UI\Dropdown\DropdownTab\DropdownTabStyle;
use Simflex\Core\Layout\Components\UI\Dropdown\DropdownTab\Layout as DropdownTab;
use Simflex\Core\Layout\Components\UI\Marker\Layout as Marker;
use Simflex\Core\Layout\Components\UI\Marker\MarkerSize;
use Simflex\Core\Layout\Components\UI\Marker\MarkerStyle;
use Simflex\Core\Layout\Components\UI\Marker\MarkerTheme;
use Simflex\Core\Layout\Components\UI\Other\Separator\Layout as Separator;
use Simflex\Core\Layout\Components\UI\Other\Separator\SeparatorOrientation;
use Simflex\Core\Layout\Components\UI\Tab\TabMain\Layout as Tab;
use Simflex\Core\Layout\Components\UI\Tab\TabMain\TabMainSize;
use Simflex\Core\Layout\Components\UI\Tab\TabMain\TabMainStyle;
use Simflex\Core\Layout\Components\UI\Input\InputMain\Layout as TextInput;
use Simflex\Core\Layout\Components\UI\Input\InputMain\InputMainKind;
use Simflex\Core\Layout\Components\UI\Input\InputMain\InputMainLabelPos;

/**
 * Базовые UI-элементы ядра (UI): классы, атрибуты, aria, экранирование, варианты enum
 */
class UiCoreTest extends Unit
{
    private mixed $configBackup = null;

    protected function _before(): void
    {
        // renderIcon() берёт тему из конфига
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => false]);
    }

    protected function _after(): void
    {
        Container::set('config', $this->configBackup);
    }

    public function testButtonIsButtonByDefault(): void
    {
        $html = Button::drawButtonMain(text: 'Отправить');

        $this->assertStringContainsString('<button', $html);
        $this->assertStringContainsString('type="button"', $html);
        $this->assertStringContainsString('class="btn btn-primary btn-size_m btn-theme_light"', $html);
        $this->assertStringContainsString('<span class="btn__text">Отправить</span>', $html);
        $this->assertStringNotContainsString('disabled', $html);
    }

    public function testButtonTypeIsNotDuplicated(): void
    {
        $html = Button::drawButtonMain(text: 'Ok', attributes: ['type' => 'submit', 'data-x' => 'a"b']);

        $this->assertSame(1, substr_count($html, 'type="submit"'));
        $this->assertStringContainsString('data-x="a&quot;b"', $html);
    }

    public function testButtonLinkWithIcons(): void
    {
        $html = Button::drawButtonMain(
            text: 'Подробнее',
            link: '/more/?a=1&b=2',
            style: ButtonMainStyle::Flat,
            size: ButtonMainSize::Small,
            iconLeft: 'phone',
            iconRight: 'arrow-up-right',
        );

        $this->assertStringContainsString('<a', $html);
        $this->assertStringContainsString('href="/more/?a=1&amp;b=2"', $html);
        $this->assertStringContainsString('btn-flat btn-size_s', $html);
        $this->assertStringContainsString('btn__icon_left', $html);
        $this->assertStringContainsString('btn__icon_right', $html);
        $this->assertStringContainsString('#icon-phone', $html);
        $this->assertStringContainsString('#icon-arrow-up-right', $html);
        $this->assertStringNotContainsString('<button', $html);
    }

    public function testButtonOldIconParametersStillWork(): void
    {
        $right = Button::drawButtonMain(text: 'Далее', icon: 'arrow-right', iconPos: ButtonMainIconPos::Right);
        $this->assertStringContainsString('btn__icon_right', $right);
        $this->assertStringNotContainsString('btn__icon_left', $right);

        $only = Button::drawButtonMain(icon: 'close', attributes: ['aria-label' => 'Закрыть']);
        $this->assertStringContainsString('btn__icon_center', $only);
        $this->assertStringContainsString('aria-label="Закрыть"', $only);
    }

    public function testButtonDisabled(): void
    {
        $button = Button::drawButtonMain(text: 'Нельзя', disabled: true);
        $this->assertStringContainsString(' disabled', $button);
        $this->assertStringContainsString('aria-disabled="true"', $button);

        $link = Button::drawButtonMain(text: 'Нельзя', link: '/x/', disabled: true);
        $this->assertStringNotContainsString('href=', $link);
        $this->assertStringContainsString('aria-disabled="true"', $link);
        $this->assertStringContainsString('tabindex="-1"', $link);
    }

    public function testButtonLoad(): void
    {
        $html = Button::drawButtonMain(text: 'Отправка', load: true);

        $this->assertStringContainsString('aria-busy="true"', $html);
        $this->assertMatchesRegularExpression('/class="[^"]*\bloading\b/', $html);
        $this->assertStringContainsString('btn__loader', $html);
    }

    public function testTabLinkActiveAndFigmaStyles(): void
    {
        $html = Tab::drawTabMain(text: 'Услуги', link: '/services/', style: TabMainStyle::Soft, size: TabMainSize::ExtraSmall, active: true);

        $this->assertStringContainsString('tab-soft tab-size_xs', $html);
        $this->assertStringContainsString('active', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertSame('filled', TabMainStyle::Filled->value);
    }

    public function testTabButtonRoleAndDisabled(): void
    {
        $html = Tab::drawTabMain(text: 'Отзывы', attributes: ['role' => 'tab'], active: true);
        $this->assertStringContainsString('aria-selected="true"', $html);
        $this->assertSame(1, substr_count($html, 'type="button"'));

        $disabled = Tab::drawTabMain(text: 'Нет', link: '/no/', disabled: true);
        $this->assertStringNotContainsString('href=', $disabled);
        $this->assertStringContainsString('aria-disabled="true"', $disabled);
    }

    public function testTabIconOnlyHasNoText(): void
    {
        $html = Tab::drawTabMain(iconLeft: 'home', attributes: ['aria-label' => 'Главная']);

        $this->assertStringNotContainsString('tab__text', $html);
        $this->assertStringContainsString('tab__icon-left', $html);
    }

    public function testDropdownTabIsDisclosure(): void
    {
        $html = DropdownTab::drawDropdownTab(
            text: 'Информация',
            link: '/info/',
            style: DropdownTabStyle::Flat,
            options: [
                ['text' => 'О нас', 'link' => '/about/', 'active' => true],
                ['text' => 'Новости', 'link' => '/news/'],
            ],
            active: true,
            id: 'info',
        );

        $this->assertStringContainsString('dropdown-tab-flat', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertStringContainsString('aria-controls="info-menu"', $html);
        $this->assertStringContainsString('id="info-menu"', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertStringNotContainsString('role="menu"', $html);
        $this->assertSame(2, substr_count($html, 'dropdown-tab__option'));
    }

    public function testMarkerStylesIconsAndLabel(): void
    {
        $default = Marker::drawMarker();
        $this->assertStringContainsString('marker marker-main marker-size_m marker-theme_light', $default);
        $this->assertStringContainsString('#icon-check', $default);
        $this->assertStringNotContainsString('role="status"', $default);

        $error = Marker::drawMarker(size: MarkerSize::Large, style: MarkerStyle::Error, theme: MarkerTheme::Dark, label: 'Ошибка "отправки"');
        $this->assertStringContainsString('marker-error marker-size_l marker-theme_dark', $error);
        $this->assertStringContainsString('#icon-close', $error);
        $this->assertStringContainsString('role="status"', $error);
        $this->assertStringContainsString('aria-label="Ошибка &quot;отправки&quot;"', $error);

        $load = Marker::drawMarker(style: MarkerStyle::Load);
        $this->assertStringContainsString('loader-icon', $load);
        $this->assertStringNotContainsString('<use', $load);
    }

    public function testBadgeCounterAndIconOnly(): void
    {
        $counter = Badge::drawBadge(text: '150', style: BadgeStyle::Error, size: BadgeSize::ExtraSmall);
        $this->assertStringContainsString('99+', $counter);
        $this->assertStringContainsString('badge-error badge-size_xs', $counter);

        $this->assertStringContainsString('>7<', Badge::drawBadge(text: '7'));

        $icon = Badge::drawBadge(icon: 'check');
        $this->assertStringContainsString('badge_icon-only', $icon);
        $this->assertStringContainsString('aria-hidden="true"', $icon);
    }

    public function testSeparatorIsDecorativeByDefault(): void
    {
        $this->assertStringContainsString('aria-hidden="true"', Separator::drawSeparator());

        $html = Separator::drawSeparator(orientation: SeparatorOrientation::Vertical, decorative: false);
        $this->assertStringContainsString('role="separator"', $html);
        $this->assertStringContainsString('aria-orientation="vertical"', $html);
        $this->assertStringNotContainsString('aria-hidden', $html);
    }

    public function testTextInputBase(): void
    {
        $html = TextInput::drawInputMain(
            placeholder: 'Имя',
            labelText: 'Ваше имя',
            labelPos: InputMainLabelPos::Standard,
            id: 'name',
            attributes: ['name' => 'name', 'required' => ''],
        );

        $this->assertStringContainsString('<label class="text-input__label" for="name">Ваше имя</label>', $html);
        $this->assertStringContainsString('id="name"', $html);
        $this->assertStringContainsString('type="text"', $html);
        $this->assertSame(1, substr_count($html, 'type="text"'));
        $this->assertStringContainsString('aria-describedby="name-error"', $html);
        $this->assertStringContainsString('id="name-error"', $html);
        $this->assertStringNotContainsString('text-input__counter', $html);
        $this->assertStringNotContainsString('aria-invalid', $html);
    }

    public function testTextInputHiddenLabelAndAccessibleName(): void
    {
        $html = TextInput::drawInputMain(placeholder: 'Поиск', labelText: 'Поиск по сайту', id: 's');

        $this->assertStringContainsString('text-input__label visually-hidden', $html);

        $noLabel = TextInput::drawInputMain(placeholder: 'Поиск', id: 's');
        $this->assertStringContainsString('aria-label="Поиск"', $noLabel);
    }

    public function testTextInputPhone(): void
    {
        $html = TextInput::drawInputMain(kind: InputMainKind::Phone, attributes: ['name' => 'phone'], id: 'ph');

        $this->assertStringContainsString('type="tel"', $html);
        $this->assertStringContainsString('inputmode="tel"', $html);
        $this->assertStringContainsString('autocomplete="tel"', $html);
        $this->assertStringContainsString('data-mask="+7 (###) ###-##-##"', $html);
        $this->assertStringContainsString('data-max="11"', $html);
        $this->assertStringContainsString('#icon-phone', $html);
        $this->assertStringContainsString('text-input-kind_phone', $html);
        $this->assertStringContainsString('<span class="text-input__counter" aria-hidden="true">0/11</span>', $html);
        $this->assertStringNotContainsString('maxlength', $html);
    }

    public function testTextInputPhoneFilledCountsDigits(): void
    {
        $html = TextInput::drawInputMain(kind: InputMainKind::Phone, value: '+7 (999) 123', id: 'ph');

        $this->assertStringContainsString('>7/11<', $html);
        $this->assertStringContainsString('text-input_filled', $html);
        $this->assertStringContainsString('value="+7 (999) 123"', $html);
    }

    public function testTextInputEmail(): void
    {
        $html = TextInput::drawInputMain(kind: InputMainKind::Email, id: 'em');

        $this->assertStringContainsString('type="email"', $html);
        $this->assertStringContainsString('autocomplete="email"', $html);
        $this->assertStringContainsString('autocapitalize="off"', $html);
        $this->assertStringContainsString('#icon-mail', $html);
        $this->assertStringContainsString('data-error-invalid="Введите корректный email"', $html);
        $this->assertStringNotContainsString('text-input__counter', $html);
    }

    public function testTextInputErrorAndEscaping(): void
    {
        $html = TextInput::drawInputMain(error: 'Заполните <поле>', value: 'a"b', id: 'x', counter: true, maxLength: 20);

        $this->assertStringContainsString('Заполните &lt;поле&gt;', $html);
        $this->assertStringContainsString('text-input_error', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('value="a&quot;b"', $html);
        $this->assertStringContainsString('maxlength="20"', $html);
        $this->assertStringContainsString('>3/20<', $html);
        $this->assertStringNotContainsString('error-message hidden', $html);
    }

    public function testTextInputDisabledClass(): void
    {
        $html = TextInput::drawInputMain(id: 'x', attributes: ['disabled' => '']);

        $this->assertStringContainsString('text-input_disabled', $html);
    }

    public function testCheckboxConsentPolicyLink(): void
    {
        $html = Checkbox::drawSwitchCheckbox(policy: true, policyUrl: '/privacy/', attributes: ['name' => 'agree', 'required' => '']);

        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertSame(1, substr_count($html, 'type="checkbox"'));
        $this->assertStringContainsString('href="/privacy/"', $html);
        $this->assertStringContainsString('name="agree"', $html);

        $this->assertStringContainsString('href="/policy"', Checkbox::drawSwitchCheckbox(policy: true));
    }

    public function testCheckboxSizeStatesAndText(): void
    {
        $html = Checkbox::drawSwitchCheckbox(
            text: 'Согласен с <a href="/p/">правилами</a>',
            size: SwitchCheckboxSize::Large,
            error: true,
            attributes: ['disabled' => ''],
        );

        $this->assertStringContainsString('checkbox-size_l', $html);
        $this->assertStringContainsString('checkbox_error', $html);
        $this->assertStringContainsString('checkbox_disabled', $html);
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertStringContainsString('<a href="/p/">правилами</a>', $html);
        $this->assertStringNotContainsString('checkbox__policy', $html);
    }
}
