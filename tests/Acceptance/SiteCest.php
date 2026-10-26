<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Страницы сайта
 */
class SiteCest
{
    public function tryToSeeHomePage(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->dontSeeErrors();
        // части макета: Page::meta(), Page::content(), Page::metaJS()
        $I->seeElementInDOM('head title');
        $I->seeElementInDOM('head meta[charset]');
        $I->seeElementInDOM('body script[src]');
        $I->dontSeeLogProblems();
    }
}
