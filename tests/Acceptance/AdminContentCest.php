<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminContentCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function tryToSeeContentList(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/content/');
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

        $date = date('Y-m-d');
        $I->openFile('uf/log/' . $date . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[emergency]');
        $I->dontSeeInThisFile('[warning]');
    }
}
