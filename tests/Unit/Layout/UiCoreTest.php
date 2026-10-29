<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\System\Container;
use Simflex\Core\Layout\Components\UI\Core\Badge\BadgeSize;
use Simflex\Core\Layout\Components\UI\Core\Badge\BadgeStyle;
use Simflex\Core\Layout\Components\UI\Core\Badge\Layout as Badge;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\ButtonIconPos;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\ButtonSize;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\ButtonStyle;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\Layout as Button;
use Simflex\Core\Layout\Components\UI\Core\Checkbox\CheckboxSize;
use Simflex\Core\Layout\Components\UI\Core\Checkbox\Layout as Checkbox;
use Simflex\Core\Layout\Components\UI\Core\DropdownTab\DropdownTabStyle;
use Simflex\Core\Layout\Components\UI\Core\DropdownTab\Layout as DropdownTab;
use Simflex\Core\Layout\Components\UI\Core\Marker\Layout as Marker;
use Simflex\Core\Layout\Components\UI\Core\Marker\MarkerSize;
use Simflex\Core\Layout\Components\UI\Core\Marker\MarkerStyle;
use Simflex\Core\Layout\Components\UI\Core\Marker\MarkerTheme;
use Simflex\Core\Layout\Components\UI\Core\Separator\Layout as Separator;
use Simflex\Core\Layout\Components\UI\Core\Separator\SeparatorOrientation;
use Simflex\Core\Layout\Components\UI\Core\Tab\Layout as Tab;
use Simflex\Core\Layout\Components\UI\Core\Tab\TabSize;
use Simflex\Core\Layout\Components\UI\Core\Tab\TabStyle;
use Simflex\Core\Layout\Components\UI\Core\TextInput\Layout as TextInput;
use Simflex\Core\Layout\Components\UI\Core\TextInput\TextInputKind;
use Simflex\Core\Layout\Components\UI\Core\TextInput\TextInputLabelPos;

/**
 * Базовые UI-элементы ядра (UI/Core): классы, атрибуты, aria, экранирование, варианты enum
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
        $html = Button::drawButton(text: 'Отправить');

        $this->assertStringContainsString('<button', $html);
        $this->assertStringContainsString('type="button"', $html);
        $this->assertStringContainsString('class="btn btn-primary btn-size_m btn-theme_light"', $html);
        $this->assertStringContainsString('<span class="btn__text">Отправить</span>', $html);
        $this->assertStringNotContainsString('disabled', $html);
    }

    public function testButtonTypeIsNotDuplicated(): void
    {
        $html = Button::drawButton(text: 'Ok', attributes: ['type' => 'submit', 'data-x' => 'a"b']);

        $this->assertSame(1, substr_count($html, 'type="submit"'));
        $this->assertStringContainsString('data-x="a&quot;b"', $html);
    }

    public function testButtonLinkWithIcons(): void
    {
        $html = Button::drawButton(
            text: 'Подробнее',
            link: '/more/?a=1&b=2',
            style: ButtonStyle::Flat,
            size: ButtonSize::Small,
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
        $right = Button::drawButton(text: 'Далее', icon: 'arrow-right', iconPos: ButtonIconPos::Right);
        $this->assertStringContainsString('btn__icon_right', $right);
        $this->assertStringNotContainsString('btn__icon_left', $right);

        $only = Button::drawButton(icon: 'close', attributes: ['aria-label' => 'Закрыть']);
        $this->assertStringContainsString('btn__icon_center', $only);
        $this->assertStringContainsString('aria-label="Закрыть"', $only);
    }

    public function testButtonDisabled(): void
    {
        $button = Button::drawButton(text: 'Нельзя', disabled: true);
        $this->assertStringContainsString(' disabled', $button);
        $this->assertStringContainsString('aria-disabled="true"', $button);

        $link = Button::drawButton(text: 'Нельзя', link: '/x/', disabled: true);
        $this->assertStringNotContainsString('href=', $link);
        $this->assertStringContainsString('aria-disabled="true"', $link);
        $this->assertStringContainsString('tabindex="-1"', $link);
    }

    public function testButtonLoad(): void
    {
        $html = Button::drawButton(text: 'Отправка', load: true);

        $this->assertStringContainsString('aria-busy="true"', $html);
        $this->assertMatchesRegularExpression('/class="[^"]*\bloading\b/', $html);
        $this->assertStringContainsString('btn__loader', $html);
    }

    public function testTabLinkActiveAndFigmaStyles(): void
    {
        $html = Tab::drawTab(text: 'Услуги', link: '/services/', style: TabStyle::Soft, size: TabSize::ExtraSmall, active: true);

        $this->assertStringContainsString('tab-soft tab-size_xs', $html);
        $this->assertStringContainsString('active', $html);
        $this->assertStringContainsString('aria-current="page"', $html);
        $this->assertSame('filled', TabStyle::Filled->value);
    }

    public function testTabButtonRoleAndDisabled(): void
    {
        $html = Tab::drawTab(text: 'Отзывы', attributes: ['role' => 'tab'], active: true);
        $this->assertStringContainsString('aria-selected="true"', $html);
        $this->assertSame(1, substr_count($html, 'type="button"'));

        $disabled = Tab::drawTab(text: 'Нет', link: '/no/', disabled: true);
        $this->assertStringNotContainsString('href=', $disabled);
        $this->assertStringContainsString('aria-disabled="true"', $disabled);
    }

    public function testTabIconOnlyHasNoText(): void
    {
        $html = Tab::drawTab(iconLeft: 'home', attributes: ['aria-label' => 'Главная']);

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
        $html = TextInput::drawInput(
            placeholder: 'Имя',
            labelText: 'Ваше имя',
            labelPos: TextInputLabelPos::Standard,
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
        $html = TextInput::drawInput(placeholder: 'Поиск', labelText: 'Поиск по сайту', id: 's');

        $this->assertStringContainsString('text-input__label visually-hidden', $html);

        $noLabel = TextInput::drawInput(placeholder: 'Поиск', id: 's');
        $this->assertStringContainsString('aria-label="Поиск"', $noLabel);
    }

    public function testTextInputPhone(): void
    {
        $html = TextInput::drawInput(kind: TextInputKind::Phone, attributes: ['name' => 'phone'], id: 'ph');

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
        $html = TextInput::drawInput(kind: TextInputKind::Phone, value: '+7 (999) 123', id: 'ph');

        $this->assertStringContainsString('>7/11<', $html);
        $this->assertStringContainsString('text-input_filled', $html);
        $this->assertStringContainsString('value="+7 (999) 123"', $html);
    }

    public function testTextInputEmail(): void
    {
        $html = TextInput::drawInput(kind: TextInputKind::Email, id: 'em');

        $this->assertStringContainsString('type="email"', $html);
        $this->assertStringContainsString('autocomplete="email"', $html);
        $this->assertStringContainsString('autocapitalize="off"', $html);
        $this->assertStringContainsString('#icon-mail', $html);
        $this->assertStringContainsString('data-error-invalid="Введите корректный email"', $html);
        $this->assertStringNotContainsString('text-input__counter', $html);
    }

    public function testTextInputErrorAndEscaping(): void
    {
        $html = TextInput::drawInput(error: 'Заполните <поле>', value: 'a"b', id: 'x', counter: true, maxLength: 20);

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
        $html = TextInput::drawInput(id: 'x', attributes: ['disabled' => '']);

        $this->assertStringContainsString('text-input_disabled', $html);
    }

    public function testCheckboxConsentPolicyLink(): void
    {
        $html = Checkbox::drawCheckbox(policy: true, policyUrl: '/privacy/', attributes: ['name' => 'agree', 'required' => '']);

        $this->assertStringContainsString('type="checkbox"', $html);
        $this->assertSame(1, substr_count($html, 'type="checkbox"'));
        $this->assertStringContainsString('href="/privacy/"', $html);
        $this->assertStringContainsString('name="agree"', $html);

        $this->assertStringContainsString('href="/policy/"', Checkbox::drawCheckbox(policy: true));
    }

    public function testCheckboxSizeStatesAndText(): void
    {
        $html = Checkbox::drawCheckbox(
            text: 'Согласен с <a href="/p/">правилами</a>',
            size: CheckboxSize::Large,
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
