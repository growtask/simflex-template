<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * "Роли" и "Пользователи"
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
        // справка поля — ещё и подсказка у заголовка колонки списка
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
        // только сведения о записи, а не вся страница админки
        $I->dontSeeElement('.modal-info__talbe-wrapper .sidebar');
        $I->see('dev@simflex', '.modal-info__talbe-wrapper');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeAccount(AcceptanceTester $I): void
    {
        // раздел без таблицы (Handlers\Account), не в боковой панели
        $I->amOnPage('/admin/account');
        $I->dontSeeErrors();
        $I->see('Данные пользователя', '.content__title');
        $I->seeInField('input[name="email"]', 'dev@simflex');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeOnlyRowsOfOwnPrivileges(AcceptanceTester $I): void
    {
        // у admin привилегии admin и manager: роль dev и её пользователь не показываются и не открываются
        $I->amOnPage('/admin/logout');
        $I->loginAsAdmin();
        $I->amOnPage('/admin/admin/role');
        $I->dontSeeErrors();
        $I->see('Администратор', '.table__body');
        $I->dontSee('Разработчик', '.table__body');
        $I->amOnPage('/admin/admin/role/?action=form&role_id=1');
        $I->see('Запись не найдена');

        $I->amOnPage('/admin/admin/user');
        $I->see('admin', '.table__body');
        $I->dontSee('dev@simflex', '.table__body');
        $I->dontSeeLogProblems();
    }

    public function tryToSaveUserKeepingPassword(AcceptanceTester $I): void
    {
        // пустой пароль в форме не меняет сохранённый
        $before = $I->grabRowsFromSql('SELECT password, name FROM user WHERE user_id = 2')[0];
        try {
            $I->amOnPage('/admin/admin/user/?action=form&user_id=2');
            $I->fillField('name', 'Codecept admin');
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', 'input[name="login"]');
            $I->dontSeeErrors();
            $I->assertSame(
                [['password' => $before['password'], 'name' => 'Codecept admin']],
                $I->grabRowsFromSql('SELECT password, name FROM user WHERE user_id = 2')
            );
        } finally {
            $I->runSql('UPDATE user SET name = ? WHERE user_id = 2', [$before['name']]);
        }
        $I->dontSeeLogProblems();
    }
}
