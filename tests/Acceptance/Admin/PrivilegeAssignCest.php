<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * "Привилегии ролей" и "Персональные привилегии": разработчик выдаёт привилегии (по имени) ролям и пользователям
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
        // разработчик видит эти разделы (MenuCest), администратор — нет
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
        $I->see('Привилегии ролей', '.page-head__title');
        $I->see('Администратор', '.table__body');
        $I->see('manager', '.table__body');
        // подсказки: что здесь значат роль и привилегия
        $I->assertStringContainsString(
            'получают все пользователи роли',
            (string)$I->executeJS("return document.querySelector('.table-data-privilege .table__head-help .tooltip').textContent")
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
        $I->submitAndWaitForReload('.edit-bar button[name="submit_apply"]', '.menu');
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
        // пользователь выбирается из существующих, а не вводится текстом
        $I->dontSeeElementInDOM('input[name="user_id"][type="text"]');
        $I->seeElementInDOM($I->dropdownItem('user_id', (string)$userId));
        $I->chooseInDropdown('user_id', $userId);
        $I->chooseInDropdown('privilege', 'dev');
        $I->submitAndWaitForReload('.edit-bar button[name="submit_apply"]', '.menu');
        $I->dontSeeErrors();
        $I->assertSame(
            [['privilege' => 'dev']],
            $I->grabRowsFromSql('SELECT privilege FROM user_privilege_personal WHERE user_id = ?', [$userId])
        );

        $I->amOnPage('/admin/admin/privilege_personal');
        $I->see('admin', '.table__body');
        $I->see('dev', '.table__body');

        // персональная привилегия работает: admin теперь видит разделы разработчика
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
        // введённый в поиск списка текст — только поиск: без выбора значение не меняется, клик снаружи закрывает список
        $I->click($I->dropdown('user_id') . '//button[@data-sf-dropdown-trigger]');
        $I->fillField($I->dropdown('user_id') . '//input[@data-sf-dropdown-search]', 'a');
        $I->wait(1);
        $I->click('.page-head__title');
        $I->dontSeeElement($I->dropdown('user_id') . '//input[@data-sf-dropdown-search]');
        $I->seeInField('input[name="user_id"]', '');

        // не сохранено: форма показана снова с ошибкой, а не пустая страница
        $I->chooseInDropdown('privilege', 'dev');
        $I->submitAndWaitForReload('.edit-bar button[name="submit_apply"]', '.menu');
        $I->see('Пользователь');
        $I->seeElementInDOM('input[name="privilege"]');
        $I->seeElement('.edit-bar button[name="submit_apply"]');
        $I->assertSame([], $I->grabRowsFromSql('SELECT id FROM user_privilege_personal'));
        $I->dontSeeLogProblems();
    }

    private function cleanup(AcceptanceTester $I): void
    {
        $I->runSql("DELETE p FROM user_role_privilege p JOIN user_role r USING(role_id) WHERE r.alias = 'user'");
        $I->runSql('DELETE FROM user_privilege_personal');
    }
}
