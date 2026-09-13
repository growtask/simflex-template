<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminContentFormCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->loginAsAdmin();
    }

    public function tryToSeeContentForm(AcceptanceTester $I)
    {
        $I->amOnPage('/admin/content/?action=form&content_id=1');
        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInTitle('Редактор страниц');

        // assets must be served from an actual URL, not a leaked filesystem path
        $I->dontSeeInPageSource('/var/www/');

        $I->seeElement('.sidebar');
        $I->seeInField('title', 'Главная');

        // dynamic content-template params (content_template_param) must render too
        $I->seeElement('input[name="meta_title"]');

        $date = date('Y-m-d');
        $I->openFile('uf/log/' . $date . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[emergency]');
        $I->dontSeeInThisFile('[warning]');
    }

    public function tryToSaveContentForm(AcceptanceTester $I)
    {
        $value = 'Codeception test ' . time();

        $I->amOnPage('/admin/content/?action=form&content_id=1');
        $I->fillField('meta_title', $value);
        $I->click('.content__btns button[name="submit_apply"]');
        $I->waitForElement('input[name="meta_title"]', 10);

        $I->dontSee('Fatal');
        $I->dontSee('error');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeInCurrentUrl('action=form');
        $I->seeInField('meta_title', $value);

        // reload from scratch to make sure the value was actually persisted, not just left in the DOM
        $I->amOnPage('/admin/content/?action=form&content_id=1');
        $I->seeInField('meta_title', $value);

        // reset back to the original state so the test is repeatable
        $I->fillField('meta_title', '');
        $I->click('.content__btns button[name="submit_apply"]');
        $I->waitForElement('input[name="meta_title"]', 10);
        $I->seeInField('meta_title', '');

        $date = date('Y-m-d');
        $I->openFile('uf/log/' . $date . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[emergency]');
        $I->dontSeeInThisFile('[warning]');
    }
}
