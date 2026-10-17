<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * "Роли" and "Пользователи"
 */
class UsersCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToSeeRoles(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/role');
        $I->dontSeeErrors();
        $I->dontSee('№ п/п');
        $I->see('Администратор', '.table__body');
        // the field's help is a hint at the column header of the list too
        $title = $I->grabAttributeFrom('.table-data-privilege .table__head-help', 'title');
        $I->assertStringContainsString('Кто управляет ролью', $title);

        $I->amOnPage('/admin/admin/role/?action=form&role_id=2');
        $I->see('Кто управляет ролью');
        $I->dontSeeElementInDOM('input[name="npp"]');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeUserInfo(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/user');
        $I->click('.tooltip-row1-info button');
        $I->waitForElementVisible('.modal-info__talbe-wrapper table', 10);
        // only the record details, not the whole admin page
        $I->dontSeeElement('.modal-info__talbe-wrapper .sidebar');
        $I->see('dev@simflex', '.modal-info__talbe-wrapper');
        $I->dontSeeLogProblems();
    }
}
