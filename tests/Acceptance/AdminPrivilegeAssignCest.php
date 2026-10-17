<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * "Привилегии ролей" and "Персональные права": the developer gives the privileges (by name) to roles and users
 */
class AdminPrivilegeAssignCest
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
        $I->see('Персональные права');

        $I->amOnPage('/admin/logout');
        $I->loginAsAdmin();
        $I->dontSee('Привилегии ролей');
        $I->dontSee('Персональные права');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeRolePrivileges(AcceptanceTester $I): void
    {
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/role_privilege');
        $I->dontSee('Fatal');
        $I->dontSee('Warning');
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
        $I->dontSee('Fatal');
        $this->chooseInDropdown($I, 'role_id', $roleId);
        $I->selectOption('select[name="privilege"]', 'manager');
        $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
        $I->dontSee('Fatal');

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
        $I->dontSee('Fatal');
        // the user is a choice from the existing users, not a text
        $I->dontSeeElementInDOM('input[name="user_id"][type="text"]');
        $I->seeElementInDOM('.form-control__dropdown-item[data-value="' . $userId . '"]');
        $this->chooseInDropdown($I, 'user_id', $userId);
        $I->selectOption('select[name="privilege"]', 'dev');
        $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
        $I->dontSee('Fatal');
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

    private function chooseInDropdown(AcceptanceTester $I, string $name, int|string $value): void
    {
        $dropdown = '//input[@name="' . $name . '"]/ancestor::div[contains(concat(" ", @class, " "), " form-control__dropdown ")][1]';
        $I->click($dropdown . '//div[contains(@class, "form-control__dropdown-top")]');
        $I->click($dropdown . '//div[contains(@class, "form-control__dropdown-item") and @data-value="' . $value . '"]');
        $I->seeInField('input[name="' . $name . '"]', (string)$value);
    }

    private function cleanup(AcceptanceTester $I): void
    {
        $I->runSql("DELETE p FROM user_role_privilege p JOIN user_role r USING(role_id) WHERE r.alias = 'user'");
        $I->runSql("DELETE p FROM user_privilege_personal p JOIN user u USING(user_id) WHERE u.login = 'admin'");
    }
}
