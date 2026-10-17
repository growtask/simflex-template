<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * The admin panel's menu: the sidebar and the "Меню" section editing it
 */
class MenuCest
{
    // menu_id=1 ("Управление") is a dev-privileged row, not visible to the plain admin account
    private const FORM = '/admin/admin/menu/?action=form&menu_id=1';
    private const NAME = 'input[name="name"].form-control__input';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToSeeManagementSectionsInOrder(AcceptanceTester $I): void
    {
        // only these: components, modules, page templates and their fields are declared in code, not edited here
        $I->amOnPage('/admin');
        $links = $I->executeJS(
            "return [...document.querySelectorAll('.sidebar a')].map(a => a.getAttribute('href'))"
            . ".filter(h => h && (h.startsWith('/admin/admin/') || h === '/admin/cron'))"
        );
        $I->assertSame([
            '/admin/admin/menu',
            '/admin/admin/privilege',
            '/admin/admin/role',
            '/admin/admin/role_privilege',
            '/admin/admin/user',
            '/admin/admin/privilege_personal',
            '/admin/cron',
        ], array_values(array_unique($links)));
    }

    public function tryToSeeForm(AcceptanceTester $I): void
    {
        $I->amOnPage(self::FORM);
        $I->dontSeeErrors();
        $I->seeInTitle('Меню');
        $I->seeInField(self::NAME, 'Управление');

        // self-referencing FK field (menu_pid -> admin_menu.menu_id) must render without a SQL error
        $I->seeElementInDOM('input[name="menu_pid"]');

        // "Модель" is a choice from the admin tables registry, not a free-text input
        $I->dontSeeElementInDOM('input[name="model"][type="text"]');
        $I->seeElementInDOM($I->dropdownItem('model', 'admin_menu'));
        $I->seeElementInDOM($I->dropdownItem('model', 'seo'));

        $I->dontSeeLogProblems();
    }

    public function tryToSaveForm(AcceptanceTester $I): void
    {
        $value = 'Codeception test menu name';
        try {
            $I->amOnPage(self::FORM);
            $I->fillField(self::NAME, $value);
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', self::NAME);
            $I->dontSeeErrors();

            // reload from scratch to make sure the value was actually persisted, not just left in the DOM
            $I->amOnPage(self::FORM);
            $I->seeInField(self::NAME, $value);
        } finally {
            // via the DB, not the UI: WebDriver's sendKeys unreliably types
            // non-Latin text into this field once it's the second fillField() in the session
            $I->runSql('UPDATE admin_menu SET name = ? WHERE menu_id = 1', ['Управление']);
        }

        $I->dontSeeLogProblems();
    }

    public function tryToChoosePrivilegeByName(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/menu/?action=form&menu_id=2');
        $I->dontSeeErrors();

        // "Привилегия" is a choice from the privileges declared in code, the row keeps the name
        $I->seeElementInDOM($I->dropdownItem('privilege', 'dev'));
        $I->seeElementInDOM($I->dropdownItem('privilege', 'manager'));
        $I->seeInField('input[name="privilege"]', 'admin');
        $I->see('admin — Привилегия администратора', $I->dropdown('privilege'));
        try {
            $I->chooseInDropdown('privilege', 'manager');
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
            $I->dontSeeErrors();
            $I->assertSame([['privilege' => 'manager']], $I->grabRowsFromSql('SELECT privilege FROM admin_menu WHERE menu_id = 2'));
        } finally {
            $I->runSql("UPDATE admin_menu SET privilege = 'admin' WHERE menu_id = 2");
        }

        $I->dontSeeLogProblems();
    }
}
