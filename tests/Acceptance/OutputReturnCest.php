<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Components, modules and admin actions return their output instead of printing it:
 * the page and ajax responses must still contain it
 */
class OutputReturnCest
{
    public function sitePageContainsLayoutParts(AcceptanceTester $I): void
    {
        $I->amOnPage('/');
        $I->dontSee('Fatal');
        // Page::meta(), Page::content(), Page::metaJS()
        $I->seeElementInDOM('head title');
        $I->seeElementInDOM('head meta[charset]');
        $I->seeElementInDOM('body script[src]');
        $I->dontSeeLogProblems();
    }

    public function adminAjaxActionsReturnResponse(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
        $I->amOnPage('/admin/content/');

        $validate = $this->fetch($I, '/admin/content/?ajax=1&action=validate', 'POST');
        $I->assertSame(200, $validate['status']);
        $I->assertArrayHasKey('valid', json_decode($validate['body'], true) ?? [], $validate['body']);

        try {
            $toggle = '/admin/content/?ajax=1&action=boolChange&field=active&pk=1';
            $I->assertSame('0', $this->fetch($I, $toggle)['body']);
            $I->assertSame('1', $this->fetch($I, $toggle)['body']);
        } finally {
            $I->runSql('UPDATE content SET active = 1 WHERE content_id = 1');
        }

        $detail = $this->fetch($I, '/admin/content/?ajax=1&action=showDetail&id=1')['body'];
        $I->assertStringContainsString('modal-info__table', $detail);
        $I->assertStringContainsString('Главная', $detail);

        $I->dontSeeLogProblems();
    }

    public function adminListContainsFiltersAndRowActions(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
        $I->amOnPage('/admin/content/');
        $I->dontSee('Fatal');
        // Base::showActions(), Base::rowActions(), Field::show()
        $I->seeElement('table');
        $I->seeElementInDOM('a[href*="action=form&content_id=1"]');
        $I->see('Главная');
        $I->dontSeeLogProblems();
    }

    /**
     * @return array{status: int, body: string}
     */
    private function fetch(AcceptanceTester $I, string $url, string $method = 'GET'): array
    {
        return $I->executeAsyncJS(
            'const done = arguments[arguments.length - 1];
            fetch(arguments[0], {
                method: arguments[1],
                credentials: "same-origin",
                headers: {"X-Requested-With": "XMLHttpRequest"},
            })
                .then(r => r.text().then(body => done({status: r.status, body})))
                .catch(e => done({status: 0, body: String(e)}));',
            [$url, $method]
        );
    }
}
