<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminMenuFormCest
{
    public function _before(AcceptanceTester $I)
    {
        // menu_id=1 ("Управление") is a dev-privileged row, not visible to the plain admin account
        $I->loginAsDev();
    }

    public function tryToSeeMenuForm(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/admin/menu/?action=form&menu_id=1');
        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInTitle('Меню');

        // assets must be served from an actual URL, not a leaked filesystem path
        $I->dontSeeInPageSource('/var/www/');

        $I->seeElement('.sidebar');
        $I->seeInField('input[name="name"].form-control__input', 'Управление');

        // self-referencing FK field (menu_pid -> admin_menu.menu_id) must render without a SQL error
        $I->seeElementInDOM('input[name="menu_pid"]');

        $date = date('Y-m-d');
        $I->openFile('uf/log/' . $date . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[emergency]');
        $I->dontSeeInThisFile('[warning]');
    }

    public function tryToSaveMenuForm(AcceptanceTester $I)
    {
        $value = 'Codeception test menu name';

        $I->amOnPage('/admin/admin/menu/?action=form&menu_id=1');
        $I->fillField('input[name="name"].form-control__input', $value);
        $I->click('.content__btns button[name="submit_apply"]');
        $I->waitForElement('input[name="name"].form-control__input', 10);

        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInField('input[name="name"].form-control__input', $value);

        // reload from scratch to make sure the value was actually persisted, not just left in the DOM
        $I->amOnPage('/admin/admin/menu/?action=form&menu_id=1');
        $I->seeInField('input[name="name"].form-control__input', $value);

        // reset back to the original state so the test is repeatable.
        // Done via direct DB write, not the UI: WebDriver's sendKeys unreliably types
        // non-Latin text into this field once it's the second fillField() in the session.
        $I->runSql('UPDATE admin_menu SET name = ? WHERE menu_id = 1', ['Управление']);
        $I->amOnPage('/admin/admin/menu/?action=form&menu_id=1');
        $I->seeInField('input[name="name"].form-control__input', 'Управление');

        $date = date('Y-m-d');
        $I->openFile('uf/log/' . $date . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[emergency]');
        $I->dontSeeInThisFile('[warning]');
    }
}
