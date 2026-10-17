<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * "Привилегии ролей" and "Персональные привилегии": the developer gives the privileges (by name) to roles and users
 */
class PrivilegeAssignCest
{
    public function _before(AcceptanceTester $I): void
    {
        $this->cleanup($I);
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->cleanup($I);
    }

    public function tryToSeeSectionsInManagementMenu(AcceptanceTester $I): void
    {
        $I->loginAsDev();
        // the management section page lists its subsections
        $I->amOnPage('/admin/admin');
        $I->see('Привилегии ролей');
        $I->see('Персональные привилегии');

        $I->amOnPage('/admin/logout');
        $I->loginAsAdmin();
        $I->dontSee('Привилегии ролей');
        $I->dontSee('Персональные привилегии');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeRolePrivileges(AcceptanceTester $I): void
    {
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/role_privilege');
        $I->dontSeeErrors();
        $I->see('Привилегии ролей', '.content__title');
        $I->see('Администратор', '.table__body');
        $I->see('manager', '.table__body');
        // the hints: what the role and the privilege mean here
        $I->assertStringContainsString(
            'получают все пользователи роли',
            $I->grabAttributeFrom('.table-data-privilege .table__head-help', 'title')
        );
        $I->seeElement('.table-data-role_id .table__head-help');
        $I->amOnPage('/admin/admin/role_privilege/?action=form');
        $I->see('Роль, пользователи которой получают привилегию');
        $I->see('Привилегия, которую получают все пользователи роли');
        $I->dontSeeLogProblems();
    }

    public function tryToGivePrivilegeToRole(AcceptanceTester $I): void
    {
        $roleId = $I->grabRowsFromSql("SELECT role_id FROM user_role WHERE alias = 'user'")[0]['role_id'];
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/role_privilege/?action=form');
        $I->dontSeeErrors();
        $I->chooseInDropdown('role_id', $roleId);
        $I->chooseInDropdown('privilege', 'manager');
        $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
        $I->dontSeeErrors();

        $I->assertSame(
            [['privilege' => 'manager']],
            $I->grabRowsFromSql('SELECT privilege FROM user_role_privilege WHERE role_id = ?', [$roleId])
        );
        $I->dontSeeLogProblems();
    }

    public function tryToGivePrivilegePersonally(AcceptanceTester $I): void
    {
        $userId = $I->grabRowsFromSql("SELECT user_id FROM user WHERE login = 'admin'")[0]['user_id'];
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/privilege_personal/?action=form');
        $I->dontSeeErrors();
        // the user is a choice from the existing users, not a text
        $I->dontSeeElementInDOM('input[name="user_id"][type="text"]');
        $I->seeElementInDOM('.form-control__dropdown-item[data-value="' . $userId . '"]');
        $I->chooseInDropdown('user_id', $userId);
        $I->chooseInDropdown('privilege', 'dev');
        $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
        $I->dontSeeErrors();
        $I->assertSame(
            [['privilege' => 'dev']],
            $I->grabRowsFromSql('SELECT privilege FROM user_privilege_personal WHERE user_id = ?', [$userId])
        );

        $I->amOnPage('/admin/admin/privilege_personal');
        $I->see('admin', '.table__body');
        $I->see('dev', '.table__body');

        // the personal privilege works: the admin sees the developer's sections now
        $I->amOnPage('/admin/logout');
        $I->loginAsAdmin();
        $I->amOnPage('/admin/admin');
        $I->see('Привилегии ролей');
        $I->dontSeeLogProblems();
    }

    public function tryToTypeInUserSearchWithoutChoosing(AcceptanceTester $I): void
    {
        $I->loginAsDev();
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

    private function cleanup(AcceptanceTester $I): void
    {
        $I->runSql("DELETE p FROM user_role_privilege p JOIN user_role r USING(role_id) WHERE r.alias = 'user'");
        $I->runSql('DELETE FROM user_privilege_personal');
    }
}
