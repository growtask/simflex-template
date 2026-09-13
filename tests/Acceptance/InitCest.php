<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class InitCest
{
    public function _before(AcceptanceTester $I): void
    {
    }

    // tests
    public function tryToTest(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');

        $I->dontSeeLogProblems();
    }
}
