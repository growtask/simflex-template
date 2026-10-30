<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

class UnderscoreLoginCest
{
    public function savedLoginWithUnderscoreCanSignIn(AcceptanceTester $I): void
    {
        $login = 'codecept_underscore';
        $I->runSql('DELETE FROM user WHERE login = ?', [$login]);
        try {
            $I->runSql('INSERT INTO user (role_id, active, login, password, email, name, code) SELECT role_id, 1, ?, ?, ?, ?, ? FROM user_role WHERE alias = ?', [$login, md5('12345'), 'underscore@example.test', 'Underscore', '', 'admin']);
            $I->amOnPage((string)(getenv('ADMIN_PATH') ?: '/admin'));
            $I->fillField('login[login]', $login);
            $I->fillField('login[password]', '12345');
            $I->click('Войти');
            $I->waitForElement('.sidebar', 10);
            $I->see($login, '.btn-user__title');
        } finally {
            $I->runSql('DELETE FROM user WHERE login = ?', [$login]);
        }
    }
}
