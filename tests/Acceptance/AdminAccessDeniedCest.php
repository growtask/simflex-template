<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminAccessDeniedCest
{
    private const LOGIN = 'codecept_noadmin';
    private const ROLE_ALIAS = 'codecept_noadmin';

    public function _before(AcceptanceTester $I): void
    {
        $this->cleanup($I);
    }

    public function _after(AcceptanceTester $I): void
    {
        $this->cleanup($I);
    }

    public function tryToOpenAdminWithoutAdminPrivilege(AcceptanceTester $I): void
    {
        // a signed-in user without the simplex_admin privilege (e.g. a site user)
        $token = md5(uniqid('', true));
        $I->runSql(
            "INSERT INTO user_role (priv_id, active, npp, name, alias)
             SELECT priv_id, 1, 99, 'Codecept no admin', ? FROM user_priv WHERE name = 'manager'",
            [self::ROLE_ALIAS]
        );
        $I->runSql(
            "INSERT INTO user (role_id, active, login, password, hash, hash_admin, email, name, code)
             SELECT role_id, 1, ?, '', '', '', 'noadmin@simflex', 'No admin', '' FROM user_role WHERE alias = ?",
            [self::LOGIN, self::ROLE_ALIAS]
        );
        $I->runSql(
            "INSERT INTO user_auth (user_id, token, time_expires, useragent, ip)
             SELECT user_id, ?, NOW() + INTERVAL 1 DAY, '', '' FROM user WHERE login = ?",
            [$token, self::LOGIN]
        );

        $I->amOnPage('/admin/');
        $I->setCookie('asft', $token);
        $I->amOnPage('/admin/');

        $I->see('Недостаточно прав для входа в панель управления');
        $I->dontSeeElement('.sidebar');
        $I->seeElement('a[href="/admin/logout/"]');

        $I->dontSeeLogProblems();
    }

    private function cleanup(AcceptanceTester $I): void
    {
        $I->runSql('DELETE a FROM user_auth a JOIN user u ON u.user_id = a.user_id WHERE u.login = ?', [self::LOGIN]);
        $I->runSql('DELETE FROM user WHERE login = ?', [self::LOGIN]);
        $I->runSql('DELETE FROM user_role WHERE alias = ?', [self::ROLE_ALIAS]);
    }
}
