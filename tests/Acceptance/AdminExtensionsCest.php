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
        $I->amOnPage('/admin/site/module');
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
        $I->seeElement($I->dropdown('module'));
        $I->seeElementInDOM($I->dropdownItem('module', 'Simflex\Extensions\Block\Block'));
        $I->dontSeeLogProblems();
    }

    public function tryToLoadModuleParams(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/module/?action=form');
        $I->chooseInDropdown('module', 'Simflex\Extensions\Block\Block');
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
        $I->seeElementInDOM($I->dropdownItem('component', 'App\Extensions\Content\Content'));
        $I->dontSeeLogProblems();
    }

    public function tryToSeeNoDbManagedExtensions(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/admin');
        $I->dontSeeLink('Компоненты');
        $I->dontSeeLink('Модули', '/admin/admin/module');
    }
}
