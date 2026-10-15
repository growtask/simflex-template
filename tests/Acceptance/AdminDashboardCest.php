<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminDashboardCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToSeeDashboard(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin');
        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInTitle('Simflex Admin');

        $I->seeElement('.sidebar');
        $I->see('Меню', '.sidebar__title');
        $I->seeElement('.header');
        $I->see('admin', '.btn-user__title');
        $I->seeElementInDOM('a[href="/admin/logout"]');

        // assets must be served from an actual URL, not a leaked filesystem path
        $I->dontSeeInPageSource('/var/www/');

        $I->dontSeeLogProblems();
    }
}
