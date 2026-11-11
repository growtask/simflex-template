<?php

namespace Tests\Unit\Layout;

use Codeception\Test\Unit;
use Simflex\Core\Layout\Components\Common\Breadcrumbs\Layout as Breadcrumbs;
use Simflex\Core\Layout\Components\Common\Pagination\Layout as Pagination;
use Simflex\Core\Layout\Components\Common\Pagination\PaginationType;
use Simflex\Core\Layout\Components\UI\Other\Placeholder\AvatarStyle;
use Simflex\Core\Layout\Components\UI\Other\Placeholder\Layout as Placeholder;
use Simflex\Core\Layout\Components\UI\Core\Buttons\ButtonIcon\ButtonIconStyle;
use Simflex\Core\Layout\Components\UI\Core\Buttons\ButtonIcon\ButtonIconTheme;
use Simflex\Core\Layout\Components\UI\Core\Buttons\ButtonIcon\Layout as ButtonIcon;
use Simflex\Core\Layout\Components\UI\Core\Dropdown\Layout as Dropdown;
use Simflex\Core\Layout\Components\Common\Modal\Layout as Modal;
use Simflex\Core\Layout\Components\UI\Core\Badge\BadgeStyle;
use Simflex\Core\Layout\Components\UI\Core\Badge\Layout as Badge;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\ButtonStyle;
use Simflex\Core\Layout\Components\UI\Core\Buttons\Button\Layout as Button;
use Simflex\Core\Layout\Components\UI\Core\Notice\Layout as Notice;
use Simflex\Core\Layout\Components\UI\Core\Tag\Layout as Tag;
use Simflex\Core\Layout\Components\UI\Core\ToggleSwitch\Layout as Toggle;
use Simflex\Core\Layout\Components\UI\Other\LoaderIcon\Layout as LoaderIcon;
use Simflex\Core\Layout\Components\UI\Other\LoaderIcon\LoaderSize;
use Simflex\Core\Layout\Components\UI\Core\SortButton\Layout as SortButton;
use Simflex\Core\Layout\Components\UI\Core\SortButton\SortButtonStyle;
use Simflex\Core\Layout\Components\UI\Core\SortButton\SortDirection;
use Simflex\Core\Layout\Components\UI\Core\Stepper\Layout as Stepper;
use Simflex\Core\Layout\Components\UI\Core\TextInput\Layout as TextInput;
use Simflex\Core\Layout\Components\UI\Core\TextInput\TextInputKind;
use Simflex\Core\Layout\Components\UI\Core\Notice\NoticeStyle;
use Simflex\Core\Layout\Components\UI\Core\Notice\NoticeTheme;
use Simflex\Core\Layout\Components\UI\Core\Toast\Layout as Toast;
use Simflex\Core\Layout\Components\UI\Core\Toast\ToastStyle;
use Simflex\Core\Layout\Components\UI\Core\Toast\ToastWidth;
use Simflex\Core\Layout\Components\UI\Core\Tooltip\Layout as Tooltip;
use Simflex\Core\Layout\Components\UI\Core\Tooltip\TooltipPosition;
use Simflex\Core\Layout\Components\UI\Core\Tooltip\TooltipStyle;
use Simflex\Core\Layout\Components\UI\Core\Tooltip\TooltipTheme;
use Simflex\Core\System\Container;

/**
 * Компоненты Layout, добавленные для админки нового дизайна: ButtonIcon, Tooltip, Notice, Toast, Breadcrumbs,
 * Pagination, Placeholder, Dropdown, SortButton, Stepper
 */
class UiAdminComponentsTest extends Unit
{
    private mixed $configBackup;

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

    public function testButtonIconHasAccessibleName(): void
    {
        $html = ButtonIcon::drawButtonIcon(
            icon: 'close',
            label: 'Закрыть "окно"',
            style: ButtonIconStyle::Secondary,
            theme: ButtonIconTheme::Dark,
        );

        $this->assertStringContainsString('<button type="button"', $html);
        $this->assertStringContainsString('class="btn-icon btn-icon-secondary btn-icon-theme_dark"', $html);
        $this->assertStringContainsString('aria-label="Закрыть &quot;окно&quot;"', $html);
        $this->assertStringContainsString('#icon-close', $html);
        $this->assertStringNotContainsString('disabled', $html);
    }

    public function testButtonIconLinkDisabled(): void
    {
        $html = ButtonIcon::drawButtonIcon(icon: 'copy', label: 'Копировать', link: '/copy', disabled: true);

        $this->assertStringStartsWith('<a', trim($html));
        $this->assertStringNotContainsString('<a href=', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
        $this->assertStringContainsString('tabindex="-1"', $html);
    }

    public function testTooltipDescribesTrigger(): void
    {
        $html = Tooltip::drawTooltip(
            '<button type="button">Сохранить</button>',
            'Сохранить <и выйти>',
            position: TooltipPosition::Left,
            style: TooltipStyle::Error,
            theme: TooltipTheme::Dark,
        );

        $this->assertMatchesRegularExpression('/<button aria-describedby="(sf-tooltip-\d+)" type="button">/', $html);
        preg_match('/aria-describedby="([^"]+)"/', $html, $match);
        $this->assertStringContainsString('id="' . $match[1] . '"', $html);
        $this->assertStringContainsString('role="tooltip"', $html);
        $this->assertStringContainsString('tooltip_position_left', $html);
        $this->assertStringContainsString('tooltip_style_error tooltip-theme_dark', $html);
        $this->assertStringContainsString('Сохранить &lt;и выйти&gt;', $html);
    }

    public function testTooltipIdsAreUnique(): void
    {
        preg_match('/id="(sf-tooltip-\d+)"/', Tooltip::drawTooltip('<span></span>', 'a'), $first);
        preg_match('/id="(sf-tooltip-\d+)"/', Tooltip::drawTooltip('<span></span>', 'b'), $second);

        $this->assertNotSame($first[1], $second[1]);
    }

    public function testTooltipInfoIsIconButton(): void
    {
        $html = Tooltip::drawInfo('Пояснение');

        $this->assertStringContainsString('tooltip-info', $html);
        $this->assertStringContainsString('aria-label="Подсказка"', $html);
        $this->assertStringContainsString('#icon-notify-info', $html);
        $this->assertStringContainsString('tooltip_position_left', $html);
    }

    public function testNoticeRoleAndIconByStyle(): void
    {
        $error = Notice::drawNotice('Ошибка', 'Проверьте поля', NoticeStyle::Error, NoticeTheme::Dark, close: true);
        $this->assertStringContainsString('role="alert"', $error);
        $this->assertStringContainsString('notice notice_style_error notice-theme_dark', $error);
        $this->assertStringContainsString('#icon-notify-close', $error);
        $this->assertStringContainsString('aria-label="Закрыть"', $error);
        $this->assertStringContainsString('Проверьте поля', $error);

        $info = Notice::drawNotice('Готово <b>', style: NoticeStyle::Success, icon: false);
        $this->assertStringContainsString('role="status"', $info);
        $this->assertStringNotContainsString('notice__icon', $info);
        $this->assertStringNotContainsString('data-sf-notice-close', $info);
        $this->assertStringNotContainsString('notice__text', $info);
        $this->assertStringContainsString('Готово &lt;b&gt;', $info);
    }

    public function testToastTimeoutAndAction(): void
    {
        $html = Toast::drawToast('Сохранено', ToastStyle::Error, ToastWidth::Fill, actionText: 'Повторить', timeout: 1000);

        $this->assertStringContainsString('role="alert"', $html);
        $this->assertStringContainsString('toast toast_style_error toast_width_fill', $html);
        // не меньше 5 секунд
        $this->assertStringContainsString('--toast-timeout: 5000ms', $html);
        $this->assertStringContainsString('<button type="button" class="toast__action">Повторить</button>', $html);
        $this->assertStringContainsString('toast__timer toast__timer_hidden', $html);
        $this->assertStringContainsString('toast__icon toast__icon_hidden', $html);
    }

    public function testToastStackHasTemplatesOfAllStyles(): void
    {
        $html = Toast::drawStack(Toast::drawToast('Есть', ToastStyle::Success));

        $this->assertStringContainsString('data-sf-toast-stack aria-live="polite"', $html);
        foreach (ToastStyle::cases() as $style) {
            $this->assertStringContainsString('data-sf-toast-template="' . $style->value . '"', $html);
        }
        // тосты вне шаблонов — только переданный
        $toasts = preg_match_all('/data-sf-toast(?![-\w])/', (string)preg_replace('#<template.*?</template>#s', '', $html));
        $this->assertSame(1, $toasts);
    }

    public function testBreadcrumbsCurrentPageIsNotLink(): void
    {
        $html = Breadcrumbs::drawBreadcrumbs([
            ['name' => 'Каталог', 'link' => '/admin/catalog'],
            ['name' => 'Товары', 'link' => '/admin/catalog/product'],
            ['name' => 'Категория', 'link' => '/admin/catalog/category'],
            ['name' => 'Ноутбук <Air>'],
        ], homeLink: '/admin');

        $this->assertStringContainsString('<nav class="breadcrumbs', $html);
        $this->assertStringContainsString('aria-label="Хлебные крошки"', $html);
        $this->assertStringContainsString('href="/admin"', $html);
        $this->assertStringContainsString('aria-label="Главная"', $html);
        $this->assertStringContainsString('href="/admin/catalog/category"', $html);
        $this->assertMatchesRegularExpression('/aria-current="page"><span class="tab__text">Ноутбук &lt;Air&gt;/', $html);
        $this->assertSame(1, substr_count($html, 'aria-current'));
        // на телефоне сворачиваются пункты между первым и родителем
        $this->assertSame(1, substr_count($html, 'breadcrumbs__item_middle'));
        $this->assertStringContainsString('data-sf-breadcrumbs-more', $html);
    }

    public function testBreadcrumbsShortPathHasNoMore(): void
    {
        $html = Breadcrumbs::drawBreadcrumbs([['name' => 'Раздел', 'link' => '/a'], ['name' => 'Запись']], homeLink: '');

        $this->assertStringNotContainsString('breadcrumbs__home', $html);
        $this->assertStringNotContainsString('data-sf-breadcrumbs-more', $html);
    }

    public function testPaginationPages(): void
    {
        $this->assertSame([1, 2, 3, 4, 5, null, 19, 20], Pagination::getPages(1, 20));
        $this->assertSame([1, null, 9, 10, 11, null, 20], Pagination::getPages(10, 20));
        $this->assertSame([1, 2, null, 16, 17, 18, 19, 20], Pagination::getPages(20, 20));
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], Pagination::getPages(4, 7));
        $this->assertSame([1, 10, 20], Pagination::getMobilePages(10, 20));
        $this->assertSame([1], Pagination::getMobilePages(1, 1));
    }

    public function testPaginationMarkup(): void
    {
        $html = Pagination::drawPagination(1, 20, '/admin/user?page={page}');

        $this->assertStringContainsString('aria-label="Страницы"', $html);
        $this->assertMatchesRegularExpression('#href="/admin/user\?page=1"[^>]*aria-current="page"#', $html);
        $this->assertStringContainsString('href="/admin/user?page=2"', $html);
        // на первой странице «назад» недоступна
        $this->assertStringContainsString('aria-label="Предыдущая страница" aria-disabled="true"', $html);
        $this->assertStringContainsString('data-url-pattern="/admin/user?page={page}"', $html);

        $compact = Pagination::drawPagination(50, 20, '?p={page}', type: PaginationType::Compact, search: false);
        $this->assertStringContainsString('20 / 20', $compact);
        $this->assertStringNotContainsString('data-sf-pagination-search', $compact);
    }

    public function testAvatarInitialsAndImage(): void
    {
        $this->assertSame('ИП', Placeholder::getInitials('иван петров сидоров'));
        $this->assertSame('A', Placeholder::getInitials(' admin '));

        $initials = Placeholder::drawAvatar('Иван Петров');
        $this->assertStringContainsString('role="img" aria-label="Иван Петров"', $initials);
        $this->assertStringContainsString('>ИП<', $initials);

        $silhouette = Placeholder::drawAvatar('', style: AvatarStyle::Initials);
        $this->assertStringContainsString('avatar_style_silhouette', $silhouette);
        $this->assertStringContainsString('#icon-user', $silhouette);

        $image = Placeholder::drawAvatar('Иван', image: '/uf/a.jpg');
        $this->assertStringContainsString('<img class="avatar', $image);
        $this->assertStringContainsString('alt="Иван"', $image);

        $this->assertStringContainsString('aria-label="Нет изображения"', Placeholder::drawNoImage());
    }

    public function testDropdownSelect(): void
    {
        $html = Dropdown::drawSelect('status', ['new' => 'Новый', 'work' => 'В <работе>'], 'work', search: true);

        $this->assertStringContainsString('<input type="hidden" name="status"', $html);
        $this->assertStringContainsString('value="work"', $html);
        $this->assertStringContainsString('aria-haspopup="listbox" aria-expanded="false"', $html);
        $this->assertStringContainsString('role="listbox"', $html);
        $this->assertSame(2, substr_count($html, 'role="option"'));
        $this->assertMatchesRegularExpression('/active dropdown__option" role="option" aria-selected="true"[^>]*data-value="work"/', $html);
        // выбранная подпись в поле, экранирована
        $this->assertStringContainsString('>В &lt;работе&gt;</span>', $html);
        $this->assertStringContainsString('data-sf-dropdown-search', $html);

        $empty = Dropdown::drawSelect('status', ['new' => 'Новый'], placeholder: 'Статус');
        $this->assertStringContainsString('dropdown__value_placeholder', $empty);
        $this->assertStringContainsString('>Статус</span>', $empty);
        $this->assertStringNotContainsString('aria-selected="true"', $empty);
    }

    public function testDropdownMenu(): void
    {
        $html = Dropdown::drawMenu('<button type="button">Ещё</button>', [
            ['text' => 'Редактировать', 'link' => '/edit'],
            ['text' => 'Удалить', 'attributes' => ['data-action' => 'delete'], 'disabled' => true],
        ]);

        $this->assertMatchesRegularExpression(
            '/<button aria-haspopup="menu" aria-expanded="false" aria-controls="(sf-dropdown-\d+)-panel"/',
            $html
        );
        $this->assertStringContainsString('role="menu"', $html);
        $this->assertSame(2, substr_count($html, 'role="menuitem"'));
        $this->assertStringContainsString('href="/edit"', $html);
        $this->assertStringContainsString('data-action="delete"', $html);
        $this->assertStringContainsString('aria-disabled="true"', $html);
    }

    public function testSortShowsActiveDirection(): void
    {
        $html = SortButton::drawSort('Название', '?sort=name', '?sort=-name', SortDirection::Descending);

        $this->assertStringContainsString('>Название</span>', $html);
        $this->assertStringContainsString('href="?sort=name"', $html);
        $this->assertStringContainsString('aria-label="Название: по возрастанию"', $html);
        $this->assertMatchesRegularExpression('/href="\?sort=-name" class="[^"]* active"[^>]*aria-pressed="true"/', $html);
        $this->assertSame(1, substr_count($html, 'aria-pressed'));
        $this->assertStringContainsString('#icon-triangle-down', $html);
        $this->assertSame('descending', SortDirection::Descending->value);

        $line = SortButton::drawSortButton(true, 'Вверх', style: SortButtonStyle::Line);
        $this->assertStringContainsString('<button type="button"', $line);
        $this->assertStringContainsString('#icon-chevron-s-up', $line);
    }

    public function testStepperLimits(): void
    {
        $html = Stepper::drawStepper('npp', 1, min: 1, max: 10, step: 1, label: 'Порядок');

        $this->assertStringContainsString('name="npp"', $html);
        $this->assertStringContainsString('value="1"', $html);
        $this->assertStringContainsString('min="1"', $html);
        $this->assertStringContainsString('max="10"', $html);
        $this->assertStringContainsString('aria-label="Порядок"', $html);
        // на минимуме минус недоступен, плюс доступен
        $this->assertMatchesRegularExpression('/aria-label="Уменьшить"[^>]*disabled/', $html);
        $this->assertDoesNotMatchRegularExpression('/aria-label="Увеличить"[^>]*disabled/', $html);

        $error = Stepper::drawStepper('qty', 5, max: 5, error: 'Осталось 5', loading: true);
        $this->assertStringContainsString('aria-invalid="true"', $error);
        $this->assertMatchesRegularExpression('/aria-describedby="(sf-stepper-\d+)-error"/', $error);
        $this->assertStringContainsString('aria-busy="true"', $error);
        $this->assertStringContainsString('readonly', $error);
        $this->assertStringNotContainsString('min=', $error);
    }

    public function testTextInputPasswordAndSuccess(): void
    {
        $html = TextInput::drawInput(placeholder: 'Пароль', attributes: ['name' => 'pass'], kind: TextInputKind::Password);

        $this->assertStringContainsString('type="password"', $html);
        $this->assertStringContainsString('autocomplete="current-password"', $html);
        $this->assertStringContainsString('data-sf-password-toggle', $html);
        $this->assertStringContainsString('aria-pressed="false"', $html);
        $this->assertStringContainsString('aria-label="Показать пароль"', $html);
        // слот справа вместо кнопки очистки
        $this->assertStringContainsString('text-input__slot', $html);
        $this->assertStringNotContainsString('text-input__reset', $html);

        $success = TextInput::drawInput(placeholder: 'Логин', value: 'admin', success: true);
        $this->assertStringContainsString('text-input_success', $success);
        $this->assertStringContainsString('aria-label="Очистить"', $success);
        $this->assertStringNotContainsString('text-input_success', TextInput::drawInput(success: true, error: 'Ошибка'));
    }

    public function testTagRemovableHasSeparateButton(): void
    {
        $removable = Tag::drawTag(text: 'Статус: новый', icon: 'filter', close: true);
        $this->assertStringStartsWith('<span class="tag', trim($removable));
        $this->assertStringContainsString('tag_removable', $removable);
        $this->assertStringContainsString('#icon-filter', $removable);
        $this->assertStringContainsString('<button type="button" class="tag__close" data-sf-tag-remove aria-label="Удалить Статус: новый">', $removable);

        // по умолчанию — тег-кнопка, как раньше; close: false — без крестика
        $this->assertStringStartsWith('<button type="button" class="tag', trim(Tag::drawTag(text: 'Фильтр')));
        $this->assertStringNotContainsString('#icon-close', Tag::drawTag(text: 'Фильтр', close: false));
    }

    public function testToggleLabelAndDisabled(): void
    {
        $html = Toggle::drawToggle(text: 'Активность', attributes: ['disabled' => 'disabled', 'name' => 'active']);

        // подпись внутри label: имя переключателя
        $this->assertMatchesRegularExpression('#^<label class="toggle[^"]*toggle_disabled">.*Активность</span>\s*</label>$#s', trim($html));
        $this->assertStringContainsString('role="switch"', $html);
        $this->assertStringContainsString('#icon-check', $html);
        $this->assertStringNotContainsString('<path', $html);
    }

    public function testModalAlertDialogAndButtonStates(): void
    {
        $this->assertStringContainsString('role="alertdialog"', Modal::drawModal('confirm', 'Удалить?', alert: true));
        $this->assertStringContainsString('role="dialog"', Modal::drawModal('info', 'Информация'));
        $this->assertStringContainsString('btn-success', Button::drawButton(text: 'Готово', style: ButtonStyle::Success));
        $this->assertStringContainsString('btn-error', Button::drawButton(text: 'Ошибка', style: ButtonStyle::Error));
    }

    public function testLoaderAndProductBadges(): void
    {
        $html = LoaderIcon::drawLoader(LoaderSize::Large, label: 'Сохранение');
        $this->assertStringContainsString('class="loader loader_size_l loader_style_primary loader-theme_light"', $html);
        $this->assertStringContainsString('role="status" aria-label="Сохранение"', $html);
        $this->assertStringContainsString('<svg class="loader-icon', $html);

        $this->assertStringContainsString('badge-hit', Badge::drawBadge(text: 'Хит', style: BadgeStyle::Hit));
        $this->assertStringContainsString('badge-3d', Badge::drawBadge(text: '3D', style: BadgeStyle::ThreeD));
    }
}
