<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Admin panel fixes: the record info modal, the search dropdown, a not saved form, the management menu,
 * the roles section
 */
class AdminUiFixesCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->runSql('DELETE FROM user_privilege_personal');
        $I->loginAsDev();
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->runSql('DELETE FROM user_privilege_personal');
    }

    public function tryToSeeRecordInfo(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/user');
        $I->click('.tooltip-row1-info button');
        $I->waitForElementVisible('.modal-info__talbe-wrapper table', 10);
        // only the record details, not the whole admin page
        $I->dontSeeElement('.modal-info__talbe-wrapper .sidebar');
        $I->see('dev@simflex', '.modal-info__talbe-wrapper');
        $I->dontSeeLogProblems();
    }

    public function tryToTypeInSearchDropdownWithoutChoosing(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/privilege_personal/?action=form');
        $I->fillField('.form-control__dropdown-text', 'a');
        $I->wait(1);
        // the typed text is only a search: without a choice it's reset when the field loses the focus
        $I->click('.content__title');
        $I->wait(1);
        $I->seeInField('.form-control__dropdown-text', '');
        $I->seeInField('input[name="user_id"]', '');

        // not saved: the form is shown again with the error, not an empty page
        $I->chooseInDropdown('privilege', 'dev');
        $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
        $I->see('Пользователь');
        $I->seeElementInDOM('input[name="privilege"]');
        $I->seeElement('.content__btns button[name="submit_apply"]');
        $I->assertSame([], $I->grabRowsFromSql('SELECT id FROM user_privilege_personal'));
        $I->dontSeeLogProblems();
    }

    public function tryToSeeNoPageTemplateSections(AcceptanceTester $I): void
    {
        // page templates and their fields are declared in code (provider/templates.php)
        $I->amOnPage('/admin/admin');
        $I->dontSee('Шаблоны страниц');
        $I->dontSee('Параметры шаблонов');
        $I->assertSame([], $I->grabRowsFromSql("SHOW TABLES LIKE 'content_template%'"));
    }

    public function tryToSeeManagementSectionsInOrder(AcceptanceTester $I): void
    {
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

    public function tryToSeeRolesSection(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/role');
        $I->dontSee('№ п/п');
        $I->see('Администратор', '.table__body');
        // the field's help is a hint at the column header of the list too
        $I->seeElement('.table-data-privilege .table__head-help');
        $title = $I->grabAttributeFrom('.table-data-privilege .table__head-help', 'title');
        $I->assertStringContainsString('Кто управляет ролью', $title);

        $I->amOnPage('/admin/admin/role/?action=form&role_id=2');
        $I->see('Кто управляет ролью');
        $I->dontSeeElementInDOM('input[name="npp"]');
        $I->dontSeeLogProblems();
    }
}
