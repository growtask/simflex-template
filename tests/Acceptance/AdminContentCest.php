<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminContentCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToSeeContentList(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/content');
        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInTitle('Редактор страниц');

        // assets must be served from an actual URL, not a leaked filesystem path
        $I->dontSeeInPageSource('/var/www/');

        $I->seeElement('.sidebar');
        $I->seeElement('table');
        $I->see('Главная');

        $I->dontSeeLogProblems();
    }
}
