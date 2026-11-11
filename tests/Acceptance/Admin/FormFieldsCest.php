<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Поля формы на компонентах Layout: дата (TextInputKind::Date + Calendar), теги (TagsInput), картинка
 * «Из библиотеки» (Uploader + файловый менеджер)
 */
class FormFieldsCest
{
    private const CONTENT_FORM = '/admin/content/page/?action=form&content_id=1';
    private const APPLY = '.edit-bar button[name="submit_apply"]';
    private const DATE = '.text-input:has(input[name="date"])';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToPickDate(AcceptanceTester $I): void
    {
        $before = $I->grabRowsFromSql('SELECT date FROM content WHERE content_id = 1')[0]['date'];
        try {
            $I->runSql("UPDATE content SET date = '2023-09-01' WHERE content_id = 1");
            $I->amOnPage(self::CONTENT_FORM);
            $I->seeInField('input[name="date"]', '01.09.2023');
            $I->seeElement('input[name="date"][placeholder="ДД.ММ.ГГГГ"]');

            // календарь открывается на выбранном дне, фокус на нём
            $I->click(self::DATE . ' [data-sf-date-toggle]');
            $I->waitForElementVisible(self::DATE . ' .text-input__calendar', 5);
            $I->see('Сентябрь', self::DATE . ' .calendar__title');
            $I->assertSame('2023-09-01', $I->executeJS('return document.activeElement.dataset.date'));

            // клавиатура: стрелка вправо — следующий день, PageDown — месяц вперёд
            $I->pressKey(self::DATE . ' .calendar__day[data-date="2023-09-01"]', \Facebook\WebDriver\WebDriverKeys::ARROW_RIGHT);
            $I->assertSame('2023-09-02', $I->executeJS('return document.activeElement.dataset.date'));
            $I->pressKey(self::DATE . ' .calendar__day[data-date="2023-09-02"]', \Facebook\WebDriver\WebDriverKeys::PAGE_DOWN);
            $I->see('Октябрь', self::DATE . ' .calendar__title');

            // месяц и год из заголовка: год → месяцы → дни
            $I->click(self::DATE . ' .calendar__year');
            $I->click(self::DATE . ' .calendar__item[data-value="2024"]');
            $I->click(self::DATE . ' .calendar__item[data-value="2"]');
            $I->see('Март', self::DATE . ' .calendar__title');
            $I->click(self::DATE . ' .calendar__day[data-date="2024-03-15"]');
            $I->waitForElementNotVisible(self::DATE . ' .text-input__calendar', 5);
            $I->seeInField('input[name="date"]', '15.03.2024');

            // дата сохраняется в формате БД
            $I->submitAndWaitForReload(self::APPLY, 'input[name="date"]');
            $I->dontSeeErrors();
            $I->assertSame('2024-03-15', substr($I->grabRowsFromSql('SELECT date FROM content WHERE content_id = 1')[0]['date'], 0, 10));
            $I->seeInField('input[name="date"]', '15.03.2024');
        } finally {
            $I->runSql('UPDATE content SET date = ? WHERE content_id = 1', [$before]);
        }
        $I->dontSeeLogProblems();
    }

    public function tryToEditTags(AcceptanceTester $I): void
    {
        // теги товара каталога: свой текст, удаление крестиком и Backspace (без сохранения)
        $I->amOnPage('/admin/catalog/product/?action=form');
        $field = '[data-sf-tags-input]:has(input[name="tags_prim"])';
        $text = $field . ' [data-sf-tags-text]';
        $I->waitForElement($text, 10);
        $I->scrollTo($field, 0, -200);
        $I->fillField($text, 'новинка');
        $I->waitForElementVisible($field . ' .tags-input__option[data-new]', 5);
        $I->see('Добавить «новинка»', $field);
        $I->pressKey($text, \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->fillField($text, 'хит');
        $I->pressKey($text, \Facebook\WebDriver\WebDriverKeys::ENTER);
        $I->seeInField($field . ' [data-sf-tags-value]', 'новинка,хит');
        $I->see('новинка', $field . ' .tags-input__tags');

        $I->executeJS('document.querySelector(arguments[0]).click()', [$field . ' .tags-input__tag[data-value="новинка"] [data-sf-tag-remove]']);
        $I->seeInField($field . ' [data-sf-tags-value]', 'хит');
        $I->pressKey($text, \Facebook\WebDriver\WebDriverKeys::BACKSPACE);
        $I->seeInField($field . ' [data-sf-tags-value]', '');
        $I->dontSeeElement($field . ' .tags-input__tag');
    }

    public function tryToPickImageFromLibrary(AcceptanceTester $I): void
    {
        // «Из библиотеки»: файл копируется в каталог поля (имя новое), в записи — имя копии
        $source = codecept_root_dir('public/uf/images/codecept-library.png');
        copy(codecept_data_dir('upload.png'), $source);
        $before = $I->grabRowsFromSql('SELECT photo FROM content WHERE content_id = 1')[0]['photo'];
        $name = '';
        try {
            $I->amOnPage(self::CONTENT_FORM);
            $uploader = '[data-sf-uploader]:has(input[name="photo"])';
            $I->executeJS('document.querySelector(arguments[0]).click()', [$uploader . ' [data-sf-uploader-library]']);
            $I->waitForElementVisible('#sf-file-manager .file-tree-row[data-fm-path="/images"]', 10);
            $I->click('#sf-file-manager .file-tree-row[data-fm-path="/images"]');
            $I->waitForElementVisible('#sf-file-manager .file-card[data-fm-path="/images/codecept-library.png"]', 5);
            $I->doubleClick('#sf-file-manager .file-card[data-fm-path="/images/codecept-library.png"]');
            $I->waitForJS('return document.querySelector(\'input[name="photo"]\').value.includes("/uf/images/content/source/")', 10);
            $name = basename($I->grabValueFrom('input[name="photo"]'));
            $I->assertNotSame('codecept-library.png', $name);

            $I->submitAndWaitForReload(self::APPLY, 'input[name="meta_title"]');
            $I->dontSeeErrors();
            $I->assertSame($name, $I->grabRowsFromSql('SELECT photo FROM content WHERE content_id = 1')[0]['photo']);
            $I->assertFileExists(codecept_root_dir('public/uf/images/content/preview/' . $name));
        } finally {
            $I->runSql('UPDATE content SET photo = ? WHERE content_id = 1', [$before]);
            unlink($source);
            foreach ($name ? glob(codecept_root_dir('public/uf/images/content/*/' . pathinfo($name, PATHINFO_FILENAME) . '.*')) : [] as $file) {
                unlink($file);
            }
        }
        $I->dontSeeLogProblems();
    }
}
