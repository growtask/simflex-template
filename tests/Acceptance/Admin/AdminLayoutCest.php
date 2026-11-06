<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Макет админки: сворачивание меню, панель действий списка по числу выбранных строк, фильтр колонки в меню шапки,
 * «где искать», фильтр через окно и теги, тост после сохранения, ошибка поля в форме, карточки списка на телефоне
 */
class AdminLayoutCest
{
    private const LIST = '/admin/content/page';
    private const FORM = '/admin/content/page/?action=form&content_id=1';
    private const APPLY = '.edit-bar button[name="submit_apply"]';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToCollapseMenu(AcceptanceTester $I): void
    {
        $isCollapsed = 'return document.documentElement.classList.contains("sf-menu-collapsed")';
        try {
            // по умолчанию меню свёрнуто, сервер сразу выводит его свёрнутым
            $I->resetCookie('sf_admin_menu_collapsed');
            $I->amOnPage('/admin');
            $I->seeElement('#sf-menu.menu_collapsed');
            $I->assertTrue($I->executeJS($isCollapsed));
            $I->click('[data-sf-menu-collapse]');
            $I->assertFalse($I->executeJS($isCollapsed));
            // раскрытое запоминается
            $I->reloadPage();
            $I->waitForElement('[data-sf-menu-collapse]', 5);
            $I->dontSeeElement('#sf-menu.menu_collapsed');
            $I->assertFalse($I->executeJS($isCollapsed));
            $I->click('[data-sf-menu-collapse]');
            $I->assertTrue($I->executeJS($isCollapsed));
        } finally {
            $I->resetCookie('sf_admin_menu_collapsed');
        }
    }

    public function tryToUseTableBar(AcceptanceTester $I): void
    {
        $I->amOnPage(self::LIST);
        $bar = '[data-sf-table-bar]';
        $I->seeElement($bar . ' [data-sf-list-action="delete"][disabled]');
        $I->dontSeeElement($bar . ' [data-sf-selected]');

        // одна строка: изменить, копировать, удалить доступны, «Выбрано: 1»
        $this->check($I, 0);
        $I->dontSeeElement($bar . ' [data-sf-list-action="edit"][disabled]');
        $I->dontSeeElement($bar . ' [data-sf-list-action="copy"][disabled]');
        $I->see('1', $bar . ' [data-sf-selected-count]');
        $I->seeElement('.tr_selected');

        // две строки — счётчик, «выбрать все» частично отмечен
        $this->check($I, 1);
        $I->see('2', $bar . ' [data-sf-selected-count]');
        $I->assertTrue($I->executeJS('return document.querySelector("[data-sf-select-all]").indeterminate'));

        // сброс выбора
        $I->click($bar . ' [data-sf-select-clear]');
        $I->dontSeeElement($bar . ' [data-sf-selected]');
        $I->seeElement($bar . ' [data-sf-list-action="delete"][disabled]');
        $I->dontSeeElement('.tr_selected');
    }

    public function tryToFilterInModalOnPhone(AcceptanceTester $I): void
    {
        $I->resizeWindow(414, 900);
        try {
            $I->amOnPage(self::LIST);
            // на телефоне список — карточки, заголовок таблицы скрыт
            $I->dontSeeElement('.table-list thead');
            $I->assertSame('flex', $I->executeJS('return getComputedStyle(document.querySelector(".table-list .tr")).display'));
            $I->seeElement('.table-list .tr .td_main');

            // фильтр через окно: тег над списком, крестик тега сбрасывает
            $I->click('.table-toolbar__filters-button');
            $field = '[data-sf-filters-modal] [data-sf-filter][name="filter[title]"]';
            $I->waitForElementVisible($field, 5);
            $I->fillField($field, 'Главная');
            $I->click('[data-sf-filters-apply]');
            $I->waitForElement('.th-tags', 10);
            $I->see('Главная', '.th-tags');
            $I->see('Главная', '.table-list');
            $I->dontSee('Корзина', '.table-list');
            $I->click('.th-tags [data-sf-filter-reset] [data-sf-tag-remove]');
            $I->waitForElementNotVisible('.th-tags', 10);
            $I->see('Корзина', '.table-list');
        } finally {
            $I->amOnPage(self::LIST . '?action=filterReset');
            $I->resizeWindow(1920, 1080);
        }
    }

    public function tryToFilterColumnInMenu(AcceptanceTester $I): void
    {
        $filter = 'th.table-data-title [data-sf-th-filter]';
        try {
            $I->amOnPage(self::LIST . '?action=filterReset');
            // меню открывается кнопкой и закрывается по Esc
            $I->dontSeeElement($filter . ' .th-filter__menu');
            $I->click($filter . ' [data-sf-th-filter-toggle]');
            $I->waitForElementVisible($filter . ' .th-filter__menu', 5);
            $I->pressKey($filter . ' [data-sf-filter]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
            $I->waitForElementNotVisible($filter . ' .th-filter__menu', 5);

            // крестик в поле очищает его, меню остаётся открытым
            $I->click($filter . ' [data-sf-th-filter-toggle]');
            $I->waitForElementVisible($filter . ' [data-sf-filter]', 5);
            $I->fillField($filter . ' [data-sf-filter]', 'abc');
            $I->waitForElementVisible($filter . ' .text-input__reset', 5);
            $I->click($filter . ' .text-input__reset');
            $I->seeInField($filter . ' [data-sf-filter]', '');
            $I->seeElement($filter . ' .th-filter__menu');

            // «Применить»: тег над списком, бейдж у кнопки колонки, «Сбросить» у колонки
            $I->fillField($filter . ' [data-sf-filter]', 'Главная');
            $I->click($filter . ' [data-sf-th-filter-apply]');
            $I->waitForElement('.th-tags', 10);
            $I->see('Заголовок: Главная', '.th-tags');
            $I->seeElement($filter . ' .th-filter__count');
            $I->dontSee('Корзина', '.table-list tbody');

            // значение списка — переключателем в меню
            $active = 'th.table-data-active [data-sf-th-filter]';
            $I->click($active . ' [data-sf-th-filter-toggle]');
            $I->waitForElementVisible($active . ' .th-filter__menu', 5);
            $I->click($active . ' .radio-option:first-child .radio-option__label');
            $I->click($active . ' [data-sf-th-filter-apply]');
            $I->waitForText('Активно: Да', 10, '.th-tags');

            // «Сбросить» у колонки снимает только её фильтр
            $I->click('th.table-data-title .th__reset');
            $I->waitForElementNotVisible($filter . ' .th-filter__count', 10);
            $I->dontSee('Заголовок:', '.th-tags');
            $I->see('Активно: Да', '.th-tags');
            $I->see('Корзина', '.table-list tbody');
        } finally {
            $I->amOnPage(self::LIST . '?action=filterReset');
        }
    }

    public function tryToTypeOnlyDigitsInIntFilter(AcceptanceTester $I): void
    {
        $filter = 'th.table-data-content_id [data-sf-th-filter]';
        try {
            // в фильтре числа вводятся только цифры
            $I->amOnPage(self::LIST . '?action=filterReset');
            $I->click($filter . ' [data-sf-th-filter-toggle]');
            $I->waitForElementVisible($filter . ' [data-sf-filter]', 5);
            $I->fillField($filter . ' [data-sf-filter]', '1a-b2');
            $I->seeInField($filter . ' [data-sf-filter]', '12');

            // не цифры в адресе — фильтра нет, список целиком
            $I->amOnPage(self::LIST . '?filter[content_id]=abc');
            $I->dontSeeErrors();
            $I->see('Корзина', '.table-list tbody');
        } finally {
            $I->amOnPage(self::LIST . '?action=filterReset');
        }
    }

    public function tryToPickDateInFilterCalendar(AcceptanceTester $I): void
    {
        // период в меню фильтра колонки даты: акции каталога, «Начало»
        $sales = '/admin/catalog/sale';
        $filter = 'th.table-data-start [data-sf-th-filter]';
        $toggle = $filter . ' .text-input:has([data-sf-date-range="from"]) [data-sf-date-toggle]';
        try {
            $I->amOnPage($sales . '?action=filterReset');
            $I->click($filter . ' [data-sf-th-filter-toggle]');
            $I->waitForElementVisible($toggle, 5);
            $I->click($toggle);
            $I->waitForElementVisible($filter . ' .calendar__day', 5);
            // выбор дня закрывает календарь, но не меню фильтра
            $I->click($filter . ' .calendar__day:not([disabled])');
            $I->waitForElementNotVisible($filter . ' .text-input__calendar', 5);
            $I->seeElement($filter . ' .th-filter__menu');
            $from = $I->grabValueFrom($filter . ' [data-sf-date-range="from"]');
            $I->assertMatchesRegularExpression('/^\d{2}\.\d{2}\.\d{4}$/', $from);
            $I->assertStringEndsWith('|', $I->grabValueFrom($filter . ' [data-sf-date-range-value]'));

            // «Применить»: тег периода
            $I->click($filter . ' [data-sf-th-filter-apply]');
            $I->waitForText('Начало: с ' . $from, 10, '.th-tags');
            $I->dontSeeErrors();
        } finally {
            $I->amOnPage($sales . '?action=filterReset');
        }
    }

    public function tryToChooseSearchScope(AcceptanceTester $I): void
    {
        $settings = '/admin/site/settings';
        try {
            $I->amOnPage($settings . '?action=filterReset');
            $I->see('Везде', '.table-scope__trigger');
            $I->click('.table-scope__trigger');
            $I->waitForElementVisible('.table-scope .dropdown__panel', 5);
            $I->click('Алиас', '.table-scope .dropdown__panel');
            $I->waitForText('Алиас', 10, '.table-scope__trigger');

            // поиск только в выбранной колонке: «Email» — подпись, алиас — email
            $I->fillField('[data-sf-search]', 'site_name');
            $I->pressKey('[data-sf-search]', \Facebook\WebDriver\WebDriverKeys::ENTER);
            $I->waitForText('Поиск (Алиас): site_name', 10, '.th-tags');
            $I->see('site_name', '.table-list tbody');
            $I->dontSee('email', '.table-list tbody');

            // крестик «сбросить всё»
            $I->click('.th-tags__clear');
            $I->waitForElementNotVisible('.th-tags', 10);
            $I->see('Везде', '.table-scope__trigger');
        } finally {
            $I->amOnPage($settings . '?action=filterReset');
        }
    }

    public function tryToSeeColumnHelpOnHover(AcceptanceTester $I): void
    {
        // подсказка help — при наведении на название колонки, иконки «i» нет; поверх прокрутки таблицы
        $I->amOnPage('/admin/admin/role');
        $I->dontSeeElement('.th .tooltip-info');
        $I->moveMouseOver('.table-data-privilege .th__label_help');
        $I->waitForElementVisible('.table-data-privilege .th__help .tooltip', 5);
        // не обрезана прокруткой таблицы: помещается в неё или выведена поверх (tooltip_fixed)
        $isVisible = <<<'JS'
            const tooltip = document.querySelector('.table-data-privilege .th__help .tooltip');
            const box = tooltip.getBoundingClientRect();
            const clip = document.querySelector('.table-card__scroll').getBoundingClientRect();
            return tooltip.classList.contains('tooltip_fixed')
                || (box.top >= clip.top && box.bottom <= clip.bottom && box.left >= clip.left && box.right <= clip.right);
            JS;
        $I->assertTrue($I->executeJS($isVisible));
        // в низкой таблице подсказка не помещается под шапкой — выводится поверх
        $I->resizeWindow(1600, 700);
        try {
            $I->executeJS('document.querySelector(".table-card__scroll").style.maxHeight = "120px"');
            $I->moveMouseOver('.page-head');
            $I->moveMouseOver('.table-data-privilege .th__label_help');
            $I->waitForElementVisible('.table-data-privilege .tooltip.tooltip_fixed', 5);
        } finally {
            $I->resizeWindow(1920, 1080);
        }
    }

    public function tryToSeeToastAfterSave(AcceptanceTester $I): void
    {
        $I->amOnPage(self::FORM);
        $I->submitAndWaitForReload(self::APPLY, 'input[name="title"]');
        $I->waitForElementVisible('.toast', 5);
        $I->see('успешно сохранена', '.toast');
    }

    public function tryToSeeFieldError(AcceptanceTester $I): void
    {
        $title = $I->grabRowsFromSql('SELECT title FROM content WHERE content_id = 1')[0]['title'];
        $I->amOnPage(self::FORM);
        // обязательное поле пустое: форма с ошибкой у поля и уведомлением, запись не меняется
        $I->executeJS('const f = document.querySelector(\'input[name="title"]\'); f.removeAttribute("required"); f.value = "";');
        $I->submitAndWaitForReload(self::APPLY, '.edit-form');
        $I->seeElement('.form-input_error[data-field="title"]');
        $I->see('Обязательно для заполнения', '.form-input_error[data-field="title"]');
        $I->seeElement('.notice');
        $I->assertSame($title, $I->grabRowsFromSql('SELECT title FROM content WHERE content_id = 1')[0]['title']);
    }

    /** Отметить строку списка: чекбокс скрыт для WebDriver — щелчок скриптом */
    private function check(AcceptanceTester $I, int $index): void
    {
        $I->executeJS('document.querySelectorAll("[data-sf-row-select]")[arguments[0]].click()', [$index]);
    }
}
