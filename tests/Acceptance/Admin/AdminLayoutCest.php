<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Макет админки: сворачивание меню, панель действий списка по числу выбранных строк, фильтр через окно и теги,
 * тост после сохранения, ошибка поля в форме, карточки списка на телефоне
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
        $I->amOnPage('/admin');
        $isCollapsed = 'return document.documentElement.classList.contains("sf-menu-collapsed")';
        $I->assertFalse($I->executeJS($isCollapsed));
        $I->click('[data-sf-menu-collapse]');
        $I->assertTrue($I->executeJS($isCollapsed));
        // свёрнутость запоминается
        $I->reloadPage();
        $I->waitForElement('[data-sf-menu-collapse]', 5);
        $I->assertTrue($I->executeJS($isCollapsed));
        $I->click('[data-sf-menu-collapse]');
        $I->assertFalse($I->executeJS($isCollapsed));
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
            $I->click('.table-toolbar__filters');
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
