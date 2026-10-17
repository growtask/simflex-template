<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * The site's pages
 */
class SiteCest
{
    public function tryToSeeHomePage(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->dontSeeErrors();
        // the parts of the layout: Page::meta(), Page::content(), Page::metaJS()
        $I->seeElementInDOM('head title');
        $I->seeElementInDOM('head meta[charset]');
        $I->seeElementInDOM('body script[src]');
        $I->dontSeeLogProblems();
    }
}
