<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\ButtonSize;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\Layout as Button;
use Simflex\Core\Layout\Components\UI\Button\ButtonMenu\ButtonMenuDirection;
use Simflex\Core\Layout\Components\UI\Button\ButtonMenu\Layout as ButtonMenu;
use Simflex\Core\Layout\Components\UI\Button\ButtonFeedback\ButtonFeedbackType;
use Simflex\Core\Layout\Components\UI\Button\ButtonFeedback\Layout as ButtonFeedback;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocial\ButtonSocialStyle;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocial\ButtonSocialType;
use Simflex\Core\Layout\Components\UI\Button\ButtonSocial\Layout as ButtonSocial;
use Simflex\Core\Layout\Components\UI\Loader\LoaderDots\Layout as LoaderDots;
use Simflex\Core\Layout\Components\UI\Loader\LoaderMain\Layout as LoaderIcon;
use Simflex\Core\Layout\Components\UI\Loader\LoaderInline\Layout as LoaderInline;
use Simflex\Core\Layout\Components\UI\Loader\LoaderOverlay\Layout as LoaderOverlay;
use Simflex\Core\Layout\Components\UI\Loader\LoaderProgress\Layout as Progress;
use Simflex\Core\Layout\Components\UI\Loader\LoaderProgressLabel\Layout as ProgressLabel;
use Simflex\Core\Layout\Components\UI\Loader\LoaderRing\Layout as Ring;
use Simflex\Core\Layout\Components\UI\Loader\LoaderSkeletonCard\Layout as SkeletonCard;
use Simflex\Core\Layout\Components\UI\Loader\LoaderSteps\Layout as Steps;
use Simflex\Core\Layout\Components\UI\Loader\LoaderProgress\ProgressType;
use Simflex\Core\Layout\Components\UI\Switch\SwitchRadioColor\Layout as RadioColor;
use Simflex\Core\Layout\Components\UI\Switch\SwitchRadioColor\RadioColorValue;
use Simflex\Core\Layout\Components\UI\Loader\LoaderSkeleton\Layout as Skeleton;
use Simflex\Core\Layout\Components\UI\Loader\LoaderSkeleton\SkeletonShape;
use Simflex\Core\Layout\Components\UI\Range\RangeMain\Layout as Range;
use Simflex\Core\Layout\Components\UI\Range\RangeMain\RangeType;
use Simflex\Core\Layout\Components\UI\Range\RangeMain\RangeWidth;
use Simflex\Core\Layout\Components\UI\Scrollbar\ScrollbarMain\Layout as Scrollbar;
use Simflex\Core\Layout\Components\UI\Scrollbar\ScrollbarMain\ScrollbarDirection;
use Simflex\Core\Layout\Components\UI\Scrollbar\ScrollbarMain\ScrollbarSize;
use Simflex\Core\Layout\Components\UI\Scrollbar\ScrollbarMain\ScrollbarStyle;
use Simflex\Core\Layout\Components\UI\Selector\SelectorMain\Layout as Selector;
use Simflex\Core\Layout\Components\UI\Selector\SelectorMain\SelectorSize;
use Simflex\Core\Layout\Components\UI\Selector\SelectorMain\SelectorStyle;
use Simflex\Core\Layout\Components\UI\Switch\SwitchTree\Layout as Tree;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarMain\CalendarFormat;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarMain\CalendarMode;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarMain\CalendarSelection;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarMain\Layout as Calendar;
use Simflex\Core\Layout\Components\UI\Input\InputCode\CodeInputState;
use Simflex\Core\Layout\Components\UI\Input\InputCode\Layout as CodeInput;
use Simflex\Core\Layout\Components\UI\Dropdown\DropdownMain\Layout as Dropdown;
use Simflex\Core\Layout\Components\UI\Input\InputLogin\Layout as LoginInput;
use Simflex\Core\Layout\Components\UI\Input\InputPhone\Layout as PhoneInput;
use Simflex\Core\Layout\Components\UI\Input\InputPhone\PhoneCountry;
use Simflex\Core\Layout\Components\UI\Input\InputSearch\Layout as SearchInput;
use Simflex\Core\Layout\Components\UI\Stepper\StepperMain\Layout as Stepper;
use Simflex\Core\Layout\Components\UI\Stepper\StepperMain\StepperStyle;
use Simflex\Core\Layout\Components\UI\Tab\TabHeading\Layout as TabHeading;
use Simflex\Core\Layout\Components\UI\Tab\TabHeading\TabHeadingSize;
use Simflex\Core\Layout\Components\UI\Tab\TabHeading\TabHeadingStyle;
use Simflex\Core\Layout\Components\UI\Tab\TabImage\Layout as TabImage;
use Simflex\Core\Layout\Components\UI\Tab\TabImage\TabImageSize;
use Simflex\Core\Layout\Components\UI\Tab\TabImage\TabImageStyle;
use Simflex\Core\Layout\Components\UI\Tab\TabMarker\Layout as TabMarker;
use Simflex\Core\Layout\Components\UI\Tab\TabMarker\TabMarkerSize;
use Simflex\Core\Layout\Components\UI\Tab\TabMarker\TabMarkerStyle;
use Simflex\Core\Layout\Components\UI\Input\InputMain\Layout as TextInput;
use Simflex\Core\Layout\Components\UI\Input\InputMain\TextInputLabelPos;
use Simflex\Core\Layout\Components\UI\Tooltip\TooltipMain\Layout as Tooltip;
use Simflex\Core\Layout\Components\UI\Tooltip\TooltipMain\TooltipPosition;
use Simflex\Core\Layout\Components\UI\Tooltip\TooltipMain\TooltipSide;
use Simflex\Core\Layout\Components\UI\Tooltip\TooltipMain\TooltipTheme;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;
use Simflex\Core\Layout\Components\UI\Tooltip\TooltipRich\Layout as TooltipRich;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\ButtonState;
use Simflex\Core\Layout\Components\UI\Tab\TabDisabled\Layout as TabDisabled;
use Simflex\Core\Layout\Components\UI\Stepper\StepperQuantity\Layout as StepperQuantity;
use Simflex\Core\Layout\Components\UI\Dropdown\DropdownInput\Layout as DropdownInput;
use Simflex\Core\Layout\Components\UI\Input\InputPassword\Layout as InputPassword;
use Simflex\Core\Layout\Components\UI\Input\InputEmail\Layout as InputEmail;
use Simflex\Core\Layout\Components\UI\Input\InputDate\Layout as InputDate;
use Simflex\Core\Layout\Components\UI\Input\InputEye\Layout as InputEye;
use Simflex\Core\Layout\Components\UI\Input\InputCalendar\Layout as InputCalendar;
use Simflex\Core\Layout\Components\UI\Input\InputFlag\Layout as InputFlag;
use Simflex\Core\Layout\Components\UI\Input\InputCountry\Layout as InputCountry;
use Simflex\Core\Layout\Components\UI\Input\InputCountryItem\Layout as InputCountryItem;
use Simflex\Core\Layout\Components\UI\Selector\SelectorItem\Layout as SelectorItem;
use Simflex\Core\Layout\Components\UI\Range\RangeDot\Layout as RangeDot;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarDay\Layout as CalendarDay;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarDay\CalendarDayState;
use Simflex\Core\Layout\Components\UI\Calendar\CalendarItem\Layout as CalendarItem;
use Simflex\Core\Layout\Components\UI\Scrollbar\ScrollbarArrow\Layout as ScrollbarArrow;
use Simflex\Core\Layout\Components\UI\Scrollbar\ScrollbarArrow\ScrollbarArrowStyle;
use Simflex\Core\Layout\Components\UI\Switch\SwitchTreeItem\Layout as TreeItem;
use Simflex\Core\Layout\Components\UI\Switch\SwitchTreeItem\TreeItemExpand;
use Simflex\Core\Layout\Components\UI\Switch\SwitchTreeItem\TreeItemState;

/**
 * Компоненты UI, добавленные и обновлённые по Figma (страница UI): варианты, которых не было в ядре
 */
class UiFigmaComponentsTest extends Unit
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

    public function testButtonMenuDirection(): void
    {
        $vertical = ButtonMenu::drawButtonMenu(text: 'Меню', badge: '3');
        $this->assertStringContainsString('btn-menu-direction_vertical', $vertical);

        $horizontal = ButtonMenu::drawButtonMenu(text: 'Меню', badge: '3', direction: ButtonMenuDirection::Horizontal);
        $this->assertStringContainsString('btn-menu-direction_horizontal', $horizontal);
        $this->assertStringContainsString('btn-menu__badge', $horizontal);
        $this->assertStringContainsString('btn-menu__text', $horizontal);

        // label=off: только иконка
        $this->assertStringNotContainsString('btn-menu__text', ButtonMenu::drawButtonMenu(link: '#', badge: '3'));
    }

    public function testButtonWithIconOnlyUsesCenterIcon(): void
    {
        $html = Button::drawButton(iconLeft: 'plus', size: ButtonSize::Small, attributes: ['aria-label' => 'Добавить']);

        $this->assertStringContainsString('btn__icon_center', $html);
        $this->assertStringNotContainsString('btn__icon_left', $html);
    }

    public function testStepperAmongShowsCurrentOfTotal(): void
    {
        $html = Stepper::drawStepper('page', 2, min: 1, max: 8, style: StepperStyle::Among);

        $this->assertStringContainsString('stepper_style_among', $html);
        $this->assertStringContainsString('data-sf-stepper-counter', $html);
        $this->assertStringContainsString('>2/8<', $html);
        $this->assertStringContainsString('icon-chevron-s-left', $html);
        $this->assertStringContainsString('icon-chevron-s-right', $html);
        // значение для формы остаётся в поле
        $this->assertStringContainsString('name="page"', $html);
        $this->assertStringContainsString('value="2"', $html);

        // у остальных стилей счётчика нет
        $this->assertStringNotContainsString('data-sf-stepper-counter', Stepper::drawStepper('qty', 1));
    }

    public function testRichTooltip(): void
    {
        $html = TooltipRich::drawRich(
            '<button type="button">Помощь</button>',
            title: 'Заголовок <b>',
            text: 'Пояснение',
            actionText: 'Подробнее',
            actionLink: '/help',
            icon: 'notify-info',
            position: TooltipPosition::Bottom,
            side: TooltipSide::Left,
            theme: TooltipTheme::Dark,
        );

        $this->assertStringContainsString('tooltip_rich', $html);
        $this->assertStringContainsString('tooltip_position_bottom', $html);
        $this->assertStringContainsString('tooltip_side_left', $html);
        $this->assertStringContainsString('tooltip-theme_dark', $html);
        $this->assertStringContainsString('role="tooltip"', $html);
        $this->assertMatchesRegularExpression('/<button aria-describedby="sf-tooltip-\d+" type="button">/', $html);
        // заголовок экранируется, действие — ссылка
        $this->assertStringContainsString('Заголовок &lt;b&gt;', $html);
        $this->assertStringContainsString('<a class="tooltip__action" href="/help">', $html);
        $this->assertStringContainsString('tooltip__icon', $html);

        $plain = TooltipRich::drawRich('<span>i</span>', title: 'Только заголовок');
        $this->assertStringNotContainsString('tooltip__action', $plain);
        $this->assertStringNotContainsString('tooltip__text', $plain);
        $this->assertStringNotContainsString('tooltip__icon', $plain);
    }

    public function testTextInputLabelInside(): void
    {
        $html = TextInput::drawInput(
            labelText: 'Имя',
            labelPos: TextInputLabelPos::Inside,
            icon: 'user',
            attributes: ['name' => 'name'],
        );

        $this->assertStringContainsString('text-input-pos_inside', $html);
        // подпись внутри контейнера поля и после input (поднимается по :placeholder-shown)
        $this->assertMatchesRegularExpression('/<input[^>]+placeholder=" "[^>]*>\s*<label class="text-input__label" for=/', $html);
        $this->assertSame(1, substr_count($html, 'text-input__label'));
    }

    public function testLoginAndSearchInputsComposeTextInput(): void
    {
        $login = LoginInput::drawLoginInput(value: 'ivan', error: 'Занято');
        $this->assertStringContainsString('login-input', $login);
        $this->assertStringContainsString('icon-user', $login);
        $this->assertStringContainsString('name="login"', $login);
        $this->assertStringContainsString('autocomplete="username"', $login);
        $this->assertStringContainsString('text-input_error', $login);
        $this->assertStringContainsString('text-input__reset', $login);

        $search = SearchInput::drawSearchInput(name: 'query');
        $this->assertStringContainsString('search-input', $search);
        $this->assertStringContainsString('icon-search', $search);
        $this->assertStringContainsString('name="query"', $search);
        $this->assertStringContainsString('role="searchbox"', $search);
        $this->assertStringContainsString('placeholder="Поиск"', $search);
    }

    public function testCalendarSelectionAndFormat(): void
    {
        $default = Calendar::drawCalendar(value: '2026-11-19');
        $this->assertStringContainsString('data-selection="single"', $default);
        $this->assertStringContainsString('data-format="24h"', $default);

        $range = Calendar::drawCalendar(
            value: '2026-11-17/2026-11-21',
            selection: CalendarSelection::Range,
            format: CalendarFormat::H12,
            mode: CalendarMode::DateTime,
        );
        $this->assertStringContainsString('data-selection="range"', $range);
        $this->assertStringContainsString('data-format="12h"', $range);
        $this->assertStringContainsString('data-value="2026-11-17/2026-11-21"', $range);
    }

    public function testDropdownScrollCanBeDisabled(): void
    {
        $options = ['a' => 'A', 'b' => 'B'];

        $this->assertStringNotContainsString('dropdown_scroll_off', Dropdown::drawSelect('x', $options));
        $this->assertStringContainsString('dropdown_scroll_off', Dropdown::drawSelect('x', $options, scroll: false));
    }

    public function testPhoneInputCountryAndMask(): void
    {
        $html = PhoneInput::drawPhoneInput(name: 'tel', value: '+375291234567');

        // страна определяется по коду в номере, номер форматируется по маске, в форму уходит полный номер
        $this->assertStringContainsString('phone-flag_by', $html);
        $this->assertStringContainsString('value="(29) 123-45-67"', $html);
        $this->assertStringContainsString('name="tel" value="375291234567"', $html);
        $this->assertStringContainsString('placeholder="(__) ___-__-__"', $html);
        $this->assertStringContainsString('autocomplete="tel-national"', $html);
        $this->assertSame(count(PhoneCountry::cases()), substr_count($html, 'role="option"'));
        $this->assertStringContainsString('aria-label="Страна: Беларусь +375"', $html);

        $empty = PhoneInput::drawPhoneInput(country: PhoneCountry::Ge, countries: [PhoneCountry::Ru, PhoneCountry::Am]);
        // страны нет в списке — берётся первая
        $this->assertStringContainsString('phone-flag_ru', $empty);
        $this->assertStringContainsString('value=""', $empty);

        $this->assertSame('(916) 123-45-67', PhoneCountry::Ru->format('9161234567999'));
        $this->assertSame('(91', PhoneCountry::Ru->format('91'));

        $disabled = PhoneInput::drawPhoneInput(value: '79161234567', attributes: ['disabled' => 'disabled']);
        $this->assertStringContainsString('phone-input_disabled', $disabled);
    }

    public function testTabMarker(): void
    {
        $html = TabMarker::drawTabMarker(
            text: 'Курьер',
            description: 'Завтра',
            icon: 'check',
            badge: '3',
            iconRight: 'chevron-m-down',
            style: TabMarkerStyle::Outline,
            size: TabMarkerSize::Large,
            attributes: ['role' => 'tab'],
            active: true,
        );

        $this->assertStringContainsString('tab-marker-style_outline', $html);
        $this->assertStringContainsString('tab-marker-size_l', $html);
        $this->assertStringContainsString('aria-selected="true"', $html);
        $this->assertStringContainsString('tab-marker__description', $html);
        $this->assertStringContainsString('tab-marker__badge', $html);
        $this->assertStringContainsString('tab-marker__chevron', $html);

        $link = TabMarker::drawTabMarker(text: 'Ссылка', link: '/x', slot: '<b>Pro</b>', active: true, disabled: true);
        $this->assertStringStartsWith('<a ', trim($link));
        $this->assertStringContainsString('aria-current="page"', $link);
        $this->assertStringNotContainsString('href=', $link);
        $this->assertStringContainsString('tab-marker__slot_custom', $link);
        $this->assertStringNotContainsString('tab-marker__description', $link);
    }

    public function testTabImage(): void
    {
        $html = TabImage::drawTabImage(
            text: 'Диваны',
            description: '124 товара',
            image: '/uf/sofa.jpg',
            imageAlt: 'Диван',
            size: TabImageSize::ExtraLarge,
            style: TabImageStyle::Secondary,
        );

        $this->assertStringContainsString('tab-image-size_xl', $html);
        $this->assertStringContainsString('tab-image-style_secondary', $html);
        $this->assertStringContainsString('<img src="/uf/sofa.jpg" alt="Диван"', $html);

        $stub = TabImage::drawTabImage(text: 'Иконка', icon: 'home');
        $this->assertStringContainsString('tab-image__img_empty', $stub);
        $this->assertStringContainsString('icon-home', $stub);
        $this->assertStringNotContainsString('<img', $stub);
    }

    public function testTabHeading(): void
    {
        $html = TabHeading::drawTabHeading(
            text: 'Отзывы',
            badge: '24',
            style: TabHeadingStyle::Filled,
            size: TabHeadingSize::Small,
            attributes: ['role' => 'tab'],
        );

        $this->assertStringContainsString('tab-heading-style_filled', $html);
        $this->assertStringContainsString('tab-heading-size_s', $html);
        $this->assertStringContainsString('aria-selected="false"', $html);
        $this->assertStringContainsString('<span class="tab-heading__badge">24</span>', $html);
        $this->assertStringNotContainsString('tab-heading__badge', TabHeading::drawTabHeading(text: 'Описание'));
    }

    public function testButtonSocial(): void
    {
        $link = ButtonSocial::drawButtonSocial('Мы в Telegram', 'https://t.me/x', style: ButtonSocialStyle::Telegram);
        $this->assertStringContainsString('<a ', $link);
        $this->assertStringContainsString('btn-social', $link);
        $this->assertStringContainsString('social-telegram', $link);

        $button = ButtonSocial::drawButtonSocial('Поделиться', type: ButtonSocialType::Square);
        $this->assertStringContainsString('<button', $button);
        $this->assertStringNotContainsString('social-telegram', $button);
    }

    public function testButtonFeedback(): void
    {
        $html = ButtonFeedback::drawButtonFeedback(count: 12, active: true);
        $this->assertStringContainsString('aria-pressed="true"', $html);
        $this->assertStringContainsString('aria-label="Нравится"', $html);
        $this->assertStringContainsString('<span class="btn-feedback__count">12</span>', $html);

        $dislike = ButtonFeedback::drawButtonFeedback(ButtonFeedbackType::Dislike);
        $this->assertStringContainsString('aria-pressed="false"', $dislike);
        $this->assertStringContainsString('aria-label="Не нравится"', $dislike);
        $this->assertStringNotContainsString('btn-feedback__count', $dislike);
    }

    public function testRadioColor(): void
    {
        $html = RadioColor::drawRadioColor('color', 'red', 'Красный', RadioColorValue::Red, checked: true);
        $this->assertStringContainsString('type="radio"', $html);
        $this->assertStringContainsString('name="color"', $html);
        $this->assertStringContainsString('checked', $html);
        $this->assertStringContainsString('Красный', $html);
    }

    public function testLoaders(): void
    {
        $this->assertStringContainsString('loader_static', LoaderIcon::drawLoader(animation: false));
        $this->assertStringNotContainsString('loader_static', LoaderIcon::drawLoader());

        $this->assertStringContainsString('loader-dots', LoaderDots::drawLoaderDots());
        $this->assertStringContainsString('Ищем', LoaderInline::drawLoaderInline('Ищем'));

        $overlay = LoaderOverlay::drawLoaderOverlay('<p>Блок</p>', active: true);
        $this->assertStringContainsString('loader-overlay_active', $overlay);
        $this->assertStringContainsString('aria-busy', $overlay);
        $this->assertStringContainsString('<p>Блок</p>', $overlay);
        $this->assertStringNotContainsString('loader-overlay_active', LoaderOverlay::drawLoaderOverlay('x', active: false));
    }

    public function testSkeleton(): void
    {
        $circle = Skeleton::drawSkeleton(SkeletonShape::Circle, width: '50%');
        $this->assertStringContainsString('skeleton_shape_circle', $circle);
        $this->assertStringContainsString('aria-hidden="true"', $circle);
        $this->assertStringNotContainsString('width:', $circle);

        $this->assertStringContainsString('style="width:50%"', Skeleton::drawSkeleton(width: '50%'));
        $this->assertStringContainsString('skeleton_static', Skeleton::drawSkeleton(animation: false));
        $this->assertStringContainsString('skeleton_shape_rect', SkeletonCard::drawSkeletonCard());
    }

    public function testProgress(): void
    {
        $bar = Progress::drawProgress(150);
        $this->assertStringContainsString('aria-valuenow="100"', $bar);
        $this->assertStringContainsString('width:100%', $bar);
        $this->assertStringContainsString('aria-valuenow="0"', Progress::drawProgress(-5));

        $indeterminate = Progress::drawProgress(type: ProgressType::Indeterminate, animation: false);
        $this->assertStringContainsString('progress_indeterminate', $indeterminate);
        $this->assertStringContainsString('progress_static', $indeterminate);
        $this->assertStringNotContainsString('aria-valuenow', $indeterminate);

        $label = ProgressLabel::drawProgressLabel(40, 'report.pdf');
        $this->assertStringContainsString('40%', $label);
        $this->assertStringContainsString('report.pdf', $label);

        $ring = Ring::drawRing(75);
        $this->assertStringContainsString('stroke-dasharray="75 100"', $ring);
        $this->assertStringNotContainsString('progress-ring__value', Ring::drawRing(75, showValue: false));

        $steps = Steps::drawSteps(9, 7, true);
        $this->assertStringContainsString('aria-valuetext="Шаг 5 из 5"', $steps);
        $this->assertSame(5, preg_match_all('/class="progress-steps__segment[ "]/', $steps));
        $this->assertSame(3, substr_count(Steps::drawSteps(1, 3), 'progress-steps__segment_done'));
    }

    public function testSelector(): void
    {
        $html = Selector::drawSelector(
            [['text' => 'Выкл', 'disabled' => true], ['text' => 'День', 'badge' => '-20%'], ['text' => 'Неделя', 'value' => 'week']],
            SelectorStyle::Primary,
            SelectorSize::Small,
            fill: true,
            name: 'period',
        );
        // выбранного нет — выбирается первый доступный сегмент
        $this->assertSame(1, substr_count($html, 'selector-item_active'));
        $this->assertStringContainsString('aria-pressed="true"', $html);
        $this->assertSame(2, substr_count($html, 'aria-pressed="false"'));
        $this->assertStringContainsString('selector-width_fill', $html);
        $this->assertStringContainsString('selector-style_primary', $html);
        $this->assertStringContainsString('data-value="week"', $html);
        $this->assertStringContainsString('selector-item__badge">-20%', $html);
        $this->assertMatchesRegularExpression('/<input type="hidden" class="selector__input" name="period" value="День">/u', $html);

        $this->assertStringNotContainsString('selector__input', Selector::drawSelector([['text' => 'A']]));
    }

    public function testTreeDerivesParentState(): void
    {
        $tree = static fn(array $children) => Tree::drawTree([
            ['label' => 'Родитель', 'count' => 7, 'expanded' => true, 'children' => $children],
        ], name: 'cat');

        $partial = $tree([['label' => 'Один', 'checked' => true], ['label' => 'Два']]);
        $this->assertStringContainsString('data-indeterminate="1"', $partial);
        $this->assertStringContainsString('name="cat[]"', $partial);
        $this->assertStringContainsString('tree-item__count">7', $partial);
        $this->assertStringContainsString('aria-expanded="true"', $partial);

        $all = $tree([['label' => 'Один', 'checked' => true], ['label' => 'Два', 'checked' => true]]);
        $this->assertStringNotContainsString('data-indeterminate', $all);
        $this->assertSame(3, substr_count($all, 'checked="checked"'));

        $none = $tree([['label' => 'Один'], ['label' => 'Два', 'disabled' => true]]);
        $this->assertStringNotContainsString('checked="checked"', $none);
        $this->assertStringContainsString('tree-item_disabled', $none);
        $this->assertStringContainsString(' hidden', Tree::drawTree([['label' => 'A', 'children' => [['label' => 'B']]]]));
    }

    public function testRange(): void
    {
        $html = Range::drawRange(0, 100, 150, -20, 5, name: 'price', labels: true, marks: true);
        // значения ограничены шкалой, «от» не больше «до»
        $this->assertStringContainsString('aria-valuenow="0"', $html);
        $this->assertStringContainsString('aria-valuenow="100"', $html);
        $this->assertStringContainsString('name="price_from"', $html);
        $this->assertStringContainsString('name="price_to"', $html);
        $this->assertSame(2, substr_count($html, 'role="slider"'));
        $this->assertSame(5, substr_count($html, 'class="range__mark"'));
        $this->assertStringContainsString('range__labels', $html);

        $single = Range::drawRange(0, 200, 50, type: RangeType::Single, width: RangeWidth::Fill, name: 'size');
        $this->assertSame(1, substr_count($single, 'role="slider"'));
        $this->assertStringContainsString('range-width_fill', $single);
        $this->assertStringContainsString('name="size"', $single);
        $this->assertStringContainsString('left:25%', $single);

        $hidden = Range::drawRange(inputs: false, disabled: true, name: 'p');
        $this->assertStringNotContainsString('range__inputs', $hidden);
        $this->assertStringContainsString('<input type="hidden" name="p_from"', $hidden);
        $this->assertStringContainsString('range_disabled', $hidden);
        $this->assertStringContainsString('aria-disabled="true"', $hidden);
    }

    public function testScrollbar(): void
    {
        $html = Scrollbar::drawScrollbar(
            '<p>Текст</p>',
            ScrollbarStyle::Overlay,
            ScrollbarDirection::Horizontal,
            ScrollbarSize::Large,
            arrows: true,
            height: '120px',
            label: 'Список',
        );
        $this->assertStringContainsString('scrollbar-style_overlay', $html);
        $this->assertStringContainsString('scrollbar_horizontal', $html);
        $this->assertStringContainsString('scrollbar-size_l', $html);
        $this->assertStringContainsString('style="height:120px"', $html);
        $this->assertStringContainsString('role="region" aria-label="Список"', $html);
        $this->assertSame(2, substr_count($html, 'data-scrollbar-step'));
        $this->assertStringContainsString('<p>Текст</p>', $html);

        $plain = Scrollbar::drawScrollbar('x');
        $this->assertStringNotContainsString('data-scrollbar-step', $plain);
        $this->assertStringContainsString('scrollbar_vertical', $plain);
    }

    public function testCodeInput(): void
    {
        $html = CodeInput::drawCodeInput(name: 'otp', length: 5, value: '12-3');

        $this->assertSame(5, substr_count($html, 'class="code-input__digit"'));
        $this->assertStringContainsString('name="otp" value="123"', $html);
        $this->assertStringContainsString('autocomplete="one-time-code"', $html);
        // сообщение скрыто, пока состояние обычное
        $this->assertMatchesRegularExpression('/<p class="code-input__message"[^>]* hidden>/', $html);

        // длина ограничена 4–6 цифрами
        $this->assertSame(6, substr_count(CodeInput::drawCodeInput(length: 12), 'class="code-input__digit"'));
        $this->assertSame(4, substr_count(CodeInput::drawCodeInput(length: 1), 'class="code-input__digit"'));

        $error = CodeInput::drawCodeInput(state: CodeInputState::Error, message: 'Неверный <код>');
        $this->assertStringContainsString('code-input_state_error', $error);
        $this->assertStringContainsString('aria-invalid="true"', $error);
        $this->assertStringContainsString('Неверный &lt;код&gt;', $error);
        $this->assertDoesNotMatchRegularExpression('/<p class="code-input__message"[^>]* hidden>/', $error);
    }

    public function testSplitSetsAreSeparateComponents(): void
    {
        $this->assertStringContainsString('btn-state_success', Button::drawButton(text: 'Готово', state: ButtonState::Success));
        $disabled = Button::drawButton(text: 'Нельзя', disabled: true);
        $this->assertStringContainsString(' disabled', $disabled);
        $this->assertStringNotContainsString('btn-state_', $disabled);
        $this->assertStringNotContainsString('btn-state_', Button::drawButton(text: 'Основная'));

        $this->assertStringContainsString(' disabled', TabDisabled::drawTabDisabled('Раздел'));
        $this->assertStringContainsString('value="1"', StepperQuantity::drawStepperQuantity(value: 0, stock: 3));
        $this->assertStringContainsString('value="3"', StepperQuantity::drawStepperQuantity(value: 9, stock: 3));
        $this->assertStringContainsString('name="color"', DropdownInput::drawDropdownInput('color', ['red' => 'Красный']));
        $this->assertStringContainsString('type="password"', InputPassword::drawInputPassword());
        $this->assertStringContainsString('type="email"', InputEmail::drawInputEmail());
        $this->assertStringContainsString('data-sf-date="date"', InputDate::drawInputDate());
        $this->assertStringContainsString('aria-pressed="true"', InputEye::drawInputEye(active: true));
        $this->assertGreaterThan(
            substr_count(InputCalendar::drawInputCalendar(open: false), 'data-sf-calendar='),
            substr_count(InputCalendar::drawInputCalendar(), 'data-sf-calendar=')
        );
    }

    public function testPartsRenderWithParentClasses(): void
    {
        $this->assertStringContainsString('phone-flag_kz', InputFlag::drawInputFlag(PhoneCountry::Kz));
        $this->assertStringContainsString('+7', InputCountryItem::drawInputCountryItem(PhoneCountry::Ru, true));
        $this->assertStringContainsString('phone-input__panel', InputCountry::drawInputCountry(PhoneCountry::Ru, open: true));
        $this->assertStringNotContainsString('phone-input__panel', InputCountry::drawInputCountry());
        $this->assertStringContainsString('selector-item_active', SelectorItem::drawSelectorItem('Месяц', active: true));
        $this->assertStringContainsString('aria-valuenow="30"', RangeDot::drawRangeDot(30));
        $this->assertStringContainsString('calendar__day_range-start', CalendarDay::drawCalendarDay(3, CalendarDayState::RangeStart));
        $this->assertStringContainsString('aria-current="date"', CalendarDay::drawCalendarDay(3, CalendarDayState::Today));
        $this->assertStringContainsString('calendar__item_current', CalendarItem::drawCalendarItem('Март', current: true));
        $this->assertStringContainsString('chevron-s-left', ScrollbarArrow::drawScrollbarArrow(ScrollbarArrowStyle::Left));
        $item = TreeItem::drawTreeItem('Категория', '5', 1, TreeItemExpand::Collapsed, TreeItemState::Partial);
        $this->assertStringContainsString('data-indeterminate', $item);
        $this->assertStringContainsString('tree-item__count', $item);
    }
}
