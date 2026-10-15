<?php

namespace Tests\Acceptance;

use Tests\Support\AcceptanceTester;

/**
 * Components and modules are declared in code (provider/components.php, provider/modules.php), not in the DB
 */
class AdminExtensionsCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToSeeModuleItemsList(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/module/');
        $I->dontSee('Fatal');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->see('Admin - Меню');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeModuleItemForm(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/module/?action=form');
        $I->dontSee('Fatal');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeElement('select[name="module"]');
        $I->seeElement('select[name="module"] option', ['value' => 'Simflex\Extensions\Block\Block']);
        $I->dontSeeLogProblems();
    }

    public function tryToLoadModuleParams(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/module/?action=form');
        $I->selectOption('select[name="module"]', 'Simflex\Extensions\Block\Block');
        $I->waitForElement('.ajax-params', 5);
        $I->dontSee('Fatal');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeMenuFormComponents(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/menu/?action=form');
        $I->dontSee('Fatal');
        $I->dontSee('Warning');
        $I->dontSee('Notice');
        $I->seeElement('select[name="component"] option', ['value' => 'App\Extensions\Content\Content']);
        $I->dontSeeLogProblems();
    }

    public function tryToSeeNoDbManagedExtensions(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin/');
        $I->dontSeeLink('Компоненты');
        $I->dontSeeLink('Модули', '/admin/admin/module/');
    }
}
