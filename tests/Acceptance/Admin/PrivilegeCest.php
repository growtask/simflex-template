<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Привилегии объявлены в коде (\Simflex\Core\Privilege, App\Core\Privilege),
 * необязательная таблица user_privilege хранит только активность, переключённую в админке
 */
class PrivilegeCest
{
    public function _before(AcceptanceTester $I): void
    {
        $this->cleanup($I);
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->cleanup($I);
    }

    public function tryToSeeAllPrivilegesFromCode(AcceptanceTester $I): void
    {
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/privilege');
        $I->dontSeeErrors();
        $I->see('dev', '.privilege-table');
        $I->see('Привилегия разработчика', '.privilege-table');
        $I->see('admin', '.privilege-table');
        $I->see('Привилегия администратора', '.privilege-table');
        $I->see('manager', '.privilege-table');
        $I->see('Привилегия менеджера', '.privilege-table');
        $I->dontSee('simplex_admin');
        $I->dontSee('Добавить');
        $I->dontSeeElement('.tooltip-row1-edit');
        $I->dontSeeElement('.tooltip-row1-delete');
        $I->dontSeeElement('.tooltip-row1-info');
        // нельзя выключить только привилегию разработчика: иначе остальные некому было бы включить обратно
        $I->seeElementInDOM('tr[data-privilege="dev"] input[disabled]');
        $I->dontSeeElementInDOM('tr[data-privilege="admin"] input[disabled]');
        $I->dontSeeElementInDOM('tr[data-privilege="manager"] input[disabled]');
        $I->seeCheckboxIsChecked('tr[data-privilege="manager"] input');
        // в БД ничего не копируется
        $I->assertSame([], $I->grabRowsFromSql('SELECT name FROM user_privilege'));
        $I->dontSeeLogProblems();
    }

    public function tryToSwitchOffPrivilegeInList(AcceptanceTester $I): void
    {
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/privilege');
        $I->click('tr[data-privilege="manager"] .form-control__switch');
        $this->waitForRows($I, [['name' => 'manager', 'active' => 0]]);

        // после перезагрузки страница показывает переключённое значение
        $I->amOnPage('/admin/admin/privilege');
        $I->dontSeeCheckboxIsChecked('tr[data-privilege="manager"] input');
        $I->dontSeeLogProblems();
    }

    public function tryToSwitchOnPrivilegeInList(AcceptanceTester $I): void
    {
        $I->runSql("INSERT INTO user_privilege SET name = 'manager', active = 0");
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/privilege');
        $I->dontSeeCheckboxIsChecked('tr[data-privilege="manager"] input');
        $I->click('tr[data-privilege="manager"] .form-control__switch');
        $this->waitForRows($I, [['name' => 'manager', 'active' => 1]]);

        $I->amOnPage('/admin/admin/privilege');
        $I->seeCheckboxIsChecked('tr[data-privilege="manager"] input');
        $I->dontSeeLogProblems();
    }

    public function tryToSwitchOffDevPrivilege(AcceptanceTester $I): void
    {
        $I->loginAsDev();
        $I->amOnPage('/admin/admin/privilege');
        $I->executeJS(
            "window.__status = null; fetch('?action=switch', {method: 'POST', headers: {'X-Requested-With': 'XMLHttpRequest'},"
            . " body: new URLSearchParams({name: 'dev', active: '0'})}).then(r => { window.__status = r.status; })"
        );
        // админка сообщает о неудачном запросе во всплывающем окне
        $I->wait(1);
        $I->seeInPopup('400 Bad Request');
        $I->acceptPopup();
        $I->waitForJS('return window.__status !== null;', 10);
        $I->assertSame(400, $I->executeJS('return window.__status;'));
        $I->assertSame([], $I->grabRowsFromSql('SELECT name FROM user_privilege'));
    }

    public function tryToCloseAdminPanelForCustomer(AcceptanceTester $I): void
    {
        // выключение привилегии admin закрывает админку для всех, кроме разработчиков
        $I->runSql("INSERT INTO user_privilege SET name = 'admin', active = 0");

        $I->amOnPage('/admin');
        $I->fillField('login[login]', 'admin');
        $I->fillField('login[password]', '12345');
        $I->click('Войти');
        $I->dontSeeElement('.sidebar');

        $I->amOnPage('/admin/logout');
        $I->loginAsDev();
        // у разработчика все привилегии: разделы, требующие admin, по-прежнему на месте
        $I->amOnPage('/admin/admin/user');
        $I->dontSeeErrors();
        $I->see('Пользователи');
        $I->see('dev', '.table__body');
        $I->dontSeeLogProblems();
    }

    private function waitForRows(AcceptanceTester $I, array $expected): void
    {
        for ($i = 0; $i < 20; $i++) {
            if ($I->grabRowsFromSql('SELECT name, active FROM user_privilege') == $expected) {
                break;
            }
            usleep(250000);
        }
        $I->assertEquals($expected, $I->grabRowsFromSql('SELECT name, active FROM user_privilege'));
    }

    private function cleanup(AcceptanceTester $I): void
    {
        $I->runSql('DELETE FROM user_privilege');
    }
}
