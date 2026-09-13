<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

class TypedPropertiesCest
{
    public function editAndSaveContent(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
        $I->amOnPage('/admin/content/?action=form&content_id=1');
        $I->seeElement('input[name="meta_title"]');
        $original = $I->grabValueFrom('input[name="meta_title"]');
        $value = 'Typed properties ' . uniqid();
        try {
            $I->fillField('meta_title', $value);
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', 'input[name="meta_title"]');
            $I->amOnPage('/admin/content/?action=form&content_id=1');
            $I->seeInField('meta_title', $value);
        } finally {
            $I->amOnPage('/admin/content/?action=form&content_id=1');
            $I->fillField('meta_title', $original);
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', 'input[name="meta_title"]');
            $I->seeInField('meta_title', $original);
        }
    }

    public function openGroupEditingAndReturnToList(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
        $I->amOnPage('/admin/content/?action=form&ids=1,1');
        $I->seeElementInDOM('input[name="group_ids"]');
        $I->seeInField('group_ids', '1,1');
        $I->dontSee('TypeError');
        $I->amOnPage('/admin/content');
        $I->seeElement('.sidebar');
        $I->see('Главная');
        $I->dontSee('TypeError');
    }
}
