<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminLoginCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function tryToSeeLoginForm(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/');
        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInTitle('Simflex Admin');

        $I->seeElement('input[name="login[login]"]');
        $I->seeElement('input[name="login[password]"]');

        // assets must be served from an actual URL, not a leaked filesystem path
        $I->dontSeeInPageSource('/var/www/');
    }

    public function tryToLoginWithWrongPassword(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/');
        $I->fillField('login[login]', 'admin');
        $I->fillField('login[password]', 'wrong-password');
        $I->click('Войти');
        $I->wait(1);

        $I->seeElement('input[name="login[login]"]');
        $I->dontSeeElement('.sidebar');
    }

    public function tryToLoginWithValidCredentials(AcceptanceTester $I)
    {
        $I->loginAsAdmin();

        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->dontSeeElement('input[name="login[password]"]');
        $I->see('admin', '.btn-user__title');
        $I->seeElementInDOM('a[href="/admin/logout/"]');

        $date = date('Y-m-d');
        $I->openFile('uf/log/' . $date . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[emergency]');
        $I->dontSeeInThisFile('[warning]');
    }
}
