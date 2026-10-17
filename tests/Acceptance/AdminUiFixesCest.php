<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Admin panel fixes: the record info modal, the search dropdown, a not saved form, the menu of a section with subitems,
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
        $I->selectOption('select[name="privilege"]', 'dev');
        $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
        $I->see('Пользователь');
        $I->seeElement('select[name="privilege"]');
        $I->seeElement('.content__btns button[name="submit_apply"]');
        $I->assertSame([], $I->grabRowsFromSql('SELECT id FROM user_privilege_personal'));
        $I->dontSeeLogProblems();
    }

    public function tryToOpenSectionHavingSubitems(AcceptanceTester $I): void
    {
        // "Шаблоны страниц" has subitems, so its menu item only opens them: the section is the first subitem
        $I->amOnPage('/admin/admin/templates');
        $I->dontSee('Fatal');
        $I->see('Шаблоны страниц', '.content');
        $I->see('Параметры шаблонов', '.content');
        $I->seeElementInDOM('.sidebar a[href="/admin/admin/content_template"]');

        $I->amOnPage('/admin/admin/content_template');
        $I->dontSee('Fatal');
        $I->see('Шаблоны страниц', '.content__title');
        $I->seeElement('.table__body-row');
        $I->dontSeeLogProblems();
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
