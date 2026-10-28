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

    /**
     * Мета-поля страницы (шаблон «Главная», блок «Мета») - в title и description, раздел «SEO» главнее страницы
     */
    public function tryToSeePageMeta(AcceptanceTester $I): void
    {
        $params = $I->grabRowsFromSql("SELECT params FROM content WHERE path = '/'")[0]['params'];
        $meta = ['meta_title' => 'Мета-заголовок', 'meta_de' => 'Мета-описание', 'meta_kw' => 'ключ'];
        try {
            $I->runSql("UPDATE content SET params = ? WHERE path = '/'", [serialize($meta + (unserialize($params) ?: []))]);
            $I->amOnPage('/');
            $I->seeInTitle('Мета-заголовок');
            $I->dontSeeInTitle('|');
            $I->seeElementInDOM('meta[name="description"][content="Мета-описание"]');
            $I->seeElementInDOM('meta[name="keywords"][content="ключ"]');
            $I->seeElementInDOM('meta[property="og:description"][content="Мета-описание"]');

            $I->runSql("INSERT INTO seo (link, title, description, keywords, metatags) VALUES ('/', 'SEO-заголовок', 'SEO-описание', '', '')");
            $I->amOnPage('/');
            $I->seeInTitle('SEO-заголовок |');
            $I->seeElementInDOM('meta[name="description"][content="SEO-описание"]');
        } finally {
            $I->runSql("DELETE FROM seo WHERE link = '/' AND title = 'SEO-заголовок'");
            $I->runSql("UPDATE content SET params = ? WHERE path = '/'", [$params]);
        }
        $I->dontSeeLogProblems();
    }
}
