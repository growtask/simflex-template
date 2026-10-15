<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class AdminGroupSaveCest
{
    public function tryToSaveGroupWithQuoteInValue(AcceptanceTester $I): void
    {
        // a quote in the value used to break the group UPDATE built by string interpolation
        $value = "Group O'Test " . uniqid();

        $I->loginAsAdmin();
        try {
            $I->amOnPage('/admin/content/?action=form&ids=1,1');
            $I->seeInField('group_ids', '1,1');
            $I->executeJS('document.querySelector(\'input[name="set[title]"]\').checked = true;');
            $I->fillField('title', $value);
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
            $I->dontSee('Fatal');
            $I->see('успешно сохранены');

            $I->amOnPage('/admin/content/?action=form&content_id=1');
            $I->seeInField('title', $value);
        } finally {
            $I->runSql('UPDATE content SET title = ? WHERE content_id = 1', ['Главная']);
        }

        $I->openFile('uf/log/' . date('Y-m-d') . '.log');
        $I->dontSeeInThisFile('[error]');
        $I->dontSeeInThisFile('[critical]');
        $I->dontSeeInThisFile('[warning]');
    }
}
