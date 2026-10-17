<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * "Редактор страниц": the list, the form with the template's fields, saving, the ajax actions of the list
 */
class ContentCest
{
    private const FORM = '/admin/content/?action=form&content_id=1';
    private const APPLY = '.content__btns button[name="submit_apply"]';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToSeeList(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/content');
        $I->dontSeeErrors();
        $I->seeInTitle('Редактор страниц');
        $I->seeElement('table');
        $I->see('Главная');
        $I->seeElementInDOM('a[href*="action=form&content_id=1"]');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeForm(AcceptanceTester $I): void
    {
        $I->amOnPage(self::FORM);
        $I->dontSeeErrors();
        $I->seeInTitle('Редактор страниц');
        $I->seeInField('title', 'Главная');

        // the fields of the page template, declared in code (provider/templates.php), in their blocks
        $I->seeInField('input[name="template"]', 'index');
        $I->see('Главная', $I->dropdown('template'));
        $I->seeElement('input[name="meta_title"]');
        $I->seeElement('input[name="seo_title_1"]');
        $I->see('SEO', '.data2__title');
        $I->see('Мета', '.data3__title');

        $I->dontSeeLogProblems();
    }

    public function tryToSaveForm(AcceptanceTester $I): void
    {
        $value = 'Codeception test ' . time();
        try {
            $I->amOnPage(self::FORM);
            $I->fillField('meta_title', $value);
            $I->submitAndWaitForReload(self::APPLY, 'input[name="meta_title"]');
            $I->dontSeeErrors();
            $I->seeInCurrentUrl('action=form');

            // reload from scratch to make sure the value was actually persisted, not just left in the DOM
            $I->amOnPage(self::FORM);
            $I->seeInField('meta_title', $value);
            // kept in content.params by the field name
            $params = unserialize($I->grabRowsFromSql('SELECT params FROM content WHERE content_id = 1')[0]['params']);
            $I->assertSame($value, $params['meta_title']);
        } finally {
            $I->amOnPage(self::FORM);
            $I->fillField('meta_title', '');
            $I->submitAndWaitForReload(self::APPLY, 'input[name="meta_title"]');
            $I->seeInField('meta_title', '');
        }

        $I->dontSeeLogProblems();
    }

    public function tryToSaveGroupWithQuoteInValue(AcceptanceTester $I): void
    {
        // a quote in the value used to break the group UPDATE built by string interpolation
        $value = "Group O'Test " . uniqid();
        try {
            $I->amOnPage('/admin/content/?action=form&ids=1,1');
            $I->seeInField('group_ids', '1,1');
            $I->executeJS('document.querySelector(\'input[name="set[title]"]\').checked = true;');
            $I->fillField('title', $value);
            $I->submitAndWaitForReload(self::APPLY, '.sidebar');
            $I->dontSeeErrors();
            $I->see('успешно сохранены');

            $I->amOnPage(self::FORM);
            $I->seeInField('title', $value);
        } finally {
            $I->runSql('UPDATE content SET title = ? WHERE content_id = 1', ['Главная']);
        }

        $I->dontSeeLogProblems();
    }

    public function tryToCallListActions(AcceptanceTester $I): void
    {
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
