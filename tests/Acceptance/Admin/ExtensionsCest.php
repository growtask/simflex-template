<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Компоненты и модули объявлены в коде (provider/components.php, provider/modules.php), а не в БД.
 * module_item размещает только модули сайта, модули админки объявлены в \Simflex\Core\Admin\AdminModules
 */
class ExtensionsCest
{
    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToSeeOnlySiteModules(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/module');
        $I->dontSeeErrors();
        $I->amOnPage('/admin/site/module/?action=form');
        $I->seeElementInDOM($I->dropdownItem('module', 'Simflex\Core\Extensions\Block\Block'));
        $I->dontSeeElementInDOM($I->dropdownItem('module', 'Simflex\Core\Admin\Modules\Menu\Menu'));
        $I->dontSeeElementInDOM($I->dropdownItem('module', 'Simflex\Core\Admin\Modules\Breadcrumbs\Breadcrumbs'));
        $I->dontSeeLogProblems();
    }

    public function tryToLoadModuleParams(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/module/?action=form');
        $I->dontSeeErrors();
        $I->chooseInDropdown('module', 'Simflex\Core\Extensions\Block\Block');
        // параметры модуля: группа «Контент» с визуальным редактором — в основную колонку на всю строку
        $I->waitForElement('.ajax-params-left .form-input_wide .tox-tinymce', 10);
        $I->dontSeeErrors();

        // другой модуль: параметры прежнего и его редактор убираются
        $I->chooseInDropdown('module', 'Simflex\Core\Extensions\Code\Code');
        $I->waitForElementNotVisible('.tox-tinymce', 5);
        $I->dontSeeElementInDOM('.tox-tinymce');
        $I->dontSeeLogProblems();
    }

    public function tryToSaveModuleWithoutChanges(AcceptanceTester $I): void
    {
        // форма показывает настоящие значения модуля по умолчанию:
        // сохранение без изменений не добавляет заголовок и обёртку
        $I->runSql(
            "INSERT INTO module_item SET module = ?, menu_id = NULL, posname = 'codecept', active = 0, npp = 0, name = 'Codecept block', params = ''",
            ['Simflex\\Core\\Extensions\\Block\\Block']
        );
        $id = $I->grabRowsFromSql("SELECT item_id FROM module_item WHERE posname = 'codecept'")[0]['item_id'];
        try {
            $I->amOnPage("/admin/site/module/?action=form&item_id=$id");
            $I->dontSeeCheckboxIsChecked('is_title');
            $I->dontSeeCheckboxIsChecked('is_wrap');
            // текст редактора параметров сохраняется вместе с формой
            $I->waitForElement('.ajax-params-left .tox-tinymce', 10);
            $I->executeJS("tinymce.get()[0].setContent('<p>Codecept text</p>')");
            $I->submitAndWaitForReload('.edit-bar button[name="submit_apply"]', '.menu');
            $I->dontSeeErrors();

            $params = unserialize($I->grabRowsFromSql('SELECT params FROM module_item WHERE item_id = ?', [$id])[0]['params']);
            $I->assertEmpty($params['is_title'] ?? null);
            $I->assertEmpty($params['is_wrap'] ?? null);
            $I->assertStringContainsString('Codecept text', $params['content'] ?? '');
        } finally {
            $I->runSql("DELETE FROM module_item WHERE posname = 'codecept'");
        }
        $I->dontSeeLogProblems();
    }

    public function tryToSeeMenuFormComponents(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/site/menu/?action=form');
        $I->dontSeeErrors();
        $I->seeElementInDOM($I->dropdownItem('component', 'App\Extensions\Content\Content'));
        $I->dontSeeLogProblems();
    }
}
