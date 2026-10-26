<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * The admin panel's menu declared in code (\Simflex\Admin\Menu, App\Admin\Menu): the sidebar, groups, breadcrumbs
 */
class MenuCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToSeeManagementSectionsInOrder(AcceptanceTester $I): void
    {
        // the menu, components, modules, page templates and their fields are declared in code, not edited here
        $I->amOnPage('/admin');
        $links = $I->executeJS(
            "return [...document.querySelectorAll('.sidebar a')].map(a => a.getAttribute('href'))"
            . ".filter(h => h && (h.startsWith('/admin/admin/') || h === '/admin/cron'))"
        );
        $I->assertSame([
            '/admin/admin/privilege',
            '/admin/admin/role',
            '/admin/admin/role_privilege',
            '/admin/admin/user',
            '/admin/admin/privilege_personal',
            '/admin/cron',
        ], array_values(array_unique($links)));
        $I->dontSeeLogProblems();
    }

    public function tryToSeeGroupPage(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site');
        $I->dontSeeErrors();
        foreach (['/admin/site/menu', '/admin/site/module', '/admin/site/settings', '/admin/site/seo'] as $link) {
            $I->seeElement('.content .list-board a[href="' . $link . '"]');
        }
        $I->dontSeeLogProblems();
    }

    public function tryToSeeSectionTitleAndBreadcrumbs(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/seo');
        $I->dontSeeErrors();
        $I->seeInTitle('SEO');
        $I->see('Настройки сайта', '.breadcrumbs');
        $I->dontSeeLogProblems();
    }
}
