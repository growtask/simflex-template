<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\UI\Accordion\Layout as Accordion;
use Simflex\Core\Layout\Components\UI\Badge\Layout as Badge;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\ButtonMainState;
use Simflex\Core\Layout\Components\UI\Button\ButtonMain\Layout as Button;
use Simflex\Core\Layout\Components\UI\Input\InputDate\Layout as InputDate;
use Simflex\Core\Layout\Components\UI\Input\InputMain\Layout as TextInput;
use Simflex\Core\Layout\Components\UI\Loader\LoaderMain\Layout as Loader;
use Simflex\Core\Layout\Components\UI\Loader\LoaderOverlay\Layout as LoaderOverlay;
use Simflex\Core\Layout\Components\UI\Marker\Layout as Marker;
use Simflex\Core\Layout\Components\UI\Marker\MarkerStyle;
use Simflex\Core\Layout\Components\UI\Other\BadgeStock\Layout as BadgeStock;
use Simflex\Core\Layout\Components\UI\Switch\SwitchSort\Layout as Sort;
use Simflex\Core\Layout\Components\UI\Switch\SwitchSort\SwitchSortDirection;
use Simflex\Core\Layout\Components\UI\Tab\TabMain\Layout as Tab;
use Simflex\Core\Layout\Components\UI\Textarea\Layout as Textarea;
use Simflex\Core\Layout\Components\UI\Textarea\TextareaState;
use Simflex\Core\System\Container;
use Simflex\Core\System\Location;

/**
 * Разметка и доступность UI после переноса движения из grotesk-ui-kit (план дизайн-системы, этапы 1–2):
 * связи aria, состояния, разметка для анимаций (галочка маркера, кольцо лоадера, индикатор сортировки)
 */
class UiMotionA11yTest extends Unit
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

    public function testAccordionLinksButtonAndRegion(): void
    {
        $closed = Accordion::drawAccordion(title: 'Доставка', description: '<p>Текст</p>', id: 'faq-1');
        $this->assertStringContainsString('aria-expanded="false"', $closed);
        $this->assertStringContainsString('aria-controls="faq-1-content"', $closed);
        $this->assertStringContainsString('aria-labelledby="faq-1-title"', $closed);
        $this->assertStringContainsString('id="faq-1-title"', $closed);
        $this->assertMatchesRegularExpression('/role="region" id="faq-1-content"[^>]*\sinert/', $closed);
        $this->assertStringNotContainsString('role="tab', $closed);
        $this->assertStringNotContainsString('data-multiple-open', $closed);

        $open = Accordion::drawAccordion(title: 'Оплата', id: 'faq-2', open: true, multipleOpen: true);
        $this->assertStringContainsString('aria-expanded="true"', $open);
        $this->assertDoesNotMatchRegularExpression('/id="faq-2-content"[^>]*\sinert/', $open);
        $this->assertStringContainsString('data-multiple-open', $open);

        // id по умолчанию у каждого пункта свой
        $this->assertNotSame(
            $this->ids(Accordion::drawAccordion(title: 'А')),
            $this->ids(Accordion::drawAccordion(title: 'Б'))
        );
    }

    public function testTabRolesAndSelection(): void
    {
        $active = Tab::drawTabMain(text: 'Описание', active: true, attributes: ['role' => 'tab']);
        $this->assertStringContainsString('aria-selected="true"', $active);
        $this->assertStringContainsString('tab ', $active);
        $this->assertStringContainsString(' active', $active);
        $this->assertStringContainsString('aria-selected="false"', Tab::drawTabMain(text: 'Отзывы', attributes: ['role' => 'tab']));
        // ссылка-вкладка: текущая страница, а не выбор
        $link = Tab::drawTabMain(text: 'Раздел', link: '/a', active: true);
        $this->assertStringContainsString('aria-current="page"', $link);
        $this->assertStringNotContainsString('aria-selected', $link);
        $disabled = Tab::drawTabMain(text: 'Нет', link: '/b', disabled: true);
        $this->assertStringContainsString('aria-disabled="true"', $disabled);
        $this->assertStringContainsString('tabindex="-1"', $disabled);
        $this->assertStringNotContainsString('href=', $disabled);
    }

    public function testFieldErrorIsLinkedToMessage(): void
    {
        $html = TextInput::drawInputMain(id: 'email', error: 'Неверная почта');
        $this->assertStringContainsString('aria-invalid="true"', $html);
        $this->assertMatchesRegularExpression('/aria-describedby="([^"]+)"/', $html);
        preg_match('/aria-describedby="([^"]+)"/', $html, $m);
        $this->assertStringContainsString('id="' . explode(' ', $m[1])[0] . '"', $html);
        $this->assertStringContainsString('Неверная почта', $html);
        $this->assertStringNotContainsString('aria-invalid', TextInput::drawInputMain(id: 'ok'));

        $area = Textarea::drawTextarea(id: 'msg', state: TextareaState::Error);
        $this->assertStringContainsString('textarea', $area);
    }

    public function testDateFieldOpensCalendarDialog(): void
    {
        $html = InputDate::drawInputDate(value: '24.11.2026');
        $this->assertStringContainsString('data-sf-date="date"', $html);
        $this->assertStringContainsString('aria-haspopup="dialog"', $html);
        $this->assertStringContainsString('aria-expanded="false"', $html);
        $this->assertMatchesRegularExpression('/class="text-input__calendar" role="dialog" aria-label="Выбор даты" hidden/', $html);
        $this->assertStringContainsString('data-sf-calendar', $html);
    }

    public function testButtonStatesAndLoad(): void
    {
        $load = Button::drawButtonMain(text: 'Отправить', load: true);
        $this->assertStringContainsString('aria-busy="true"', $load);
        $this->assertStringContainsString('loading', $load);
        $this->assertStringContainsString('btn__loader', $load);
        $this->assertStringContainsString(' disabled', Button::drawButtonMain(text: 'Нет', disabled: true));
        $this->assertStringContainsString('btn-state_success', Button::drawButtonMain(text: 'Готово', state: ButtonMainState::Success));
    }

    public function testMarkerDrawsCheckForMainAndSuccess(): void
    {
        foreach ([MarkerStyle::Main, MarkerStyle::Success] as $style) {
            $html = Marker::drawMarker(style: $style);
            $this->assertStringContainsString('marker__check', $html, $style->value);
            $this->assertStringContainsString('pathLength="1"', $html);
        }
        // своя иконка и остальные стили — иконка спрайта
        $this->assertStringNotContainsString('marker__check', Marker::drawMarker(icon: 'star'));
        $this->assertStringNotContainsString('marker__check', Marker::drawMarker(style: MarkerStyle::Error));
    }

    public function testLoaderRingAndStaticFrame(): void
    {
        $html = Loader::drawLoaderMain(label: 'Загрузка');
        $this->assertStringContainsString('role="status"', $html);
        $this->assertStringContainsString('loader__ring', $html);
        $this->assertStringNotContainsString('loader_static', $html);
        $this->assertStringContainsString('loader_static', Loader::drawLoaderMain(animation: false));

        $overlay = LoaderOverlay::drawLoaderOverlay('<p>Блок</p>');
        $this->assertStringContainsString('loader-overlay_active', $overlay);
        $this->assertStringContainsString('aria-busy="true"', $overlay);
        $this->assertStringNotContainsString('aria-busy', LoaderOverlay::drawLoaderOverlay('<p>Блок</p>', active: false));
    }

    public function testBadgeCountersAndStockText(): void
    {
        $this->assertStringContainsString('>0</span>', Badge::drawBadge(text: '0'));
        $this->assertStringContainsString('99+', Badge::drawBadge(text: '150'));
        $this->assertStringContainsString('badge__dot', Badge::drawBadge(dot: true));
        $this->assertStringContainsString('badge-stock__text', BadgeStock::drawBadgeStock(text: 'В наличии'));
    }

    public function testSortGlideFollowsDirection(): void
    {
        $links = Sort::drawSwitchSort('Цена', '?sort=price', '?sort=-price', SwitchSortDirection::Descending);
        $this->assertStringContainsString('class="sort__glide" data-direction="descending" aria-hidden="true"', $links);
        $this->assertSame(2, substr_count($links, '<a href='));

        // без ссылок — кнопки сортировки на месте (script.js)
        $buttons = Sort::drawSwitchSort('Цена', '', '');
        $this->assertStringContainsString('data-sf-sort', $buttons);
        $this->assertStringContainsString('data-direction="none"', $buttons);
        $this->assertSame(2, substr_count($buttons, '<button type="button" class="sort-button'));
    }

    /** @return list<string> */
    private function ids(string $html): array
    {
        preg_match_all('/\sid="([^"]+)"/', $html, $m);
        return $m[1];
    }
}
