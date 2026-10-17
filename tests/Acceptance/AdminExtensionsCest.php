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

    public function tryToSaveModuleWithoutChanges(AcceptanceTester $I): void
    {
        // the form shows the module's real defaults: saving it unchanged doesn't add a title and a wrapper
        $id = $I->grabRowsFromSql("SELECT item_id FROM module_item WHERE posname = 'breadcrumbs'")[0]['item_id'];
        $params = $I->grabRowsFromSql('SELECT params FROM module_item WHERE item_id = ?', [$id])[0]['params'];
        try {
            $I->amOnPage("/admin/site/module/?action=form&item_id=$id");
            $I->dontSeeCheckboxIsChecked('is_title');
            $I->dontSeeCheckboxIsChecked('is_wrap');
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
            $I->dontSee('Fatal');

            $I->amOnPage('/admin/site/module');
            $I->seeElement('.breadcrumbs__list > .breadcrumbs__item');
            $I->dontSee('Admin - Хлебные крошки', '.header__breadcrumbs');
        } finally {
            $I->runSql('UPDATE module_item SET params = ? WHERE item_id = ?', [$params, $id]);
        }
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
