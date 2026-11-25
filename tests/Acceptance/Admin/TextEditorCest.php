<?php

namespace Tests\Acceptance\Admin;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;

/**
 * Визуальный редактор полей админки — texted (пакет simflex/texted, Plugins\Editor\Editor ядра): поле с editorFull
 * открывается редактором без ошибок, текст сохраняется в базу очищенным на сервере, медиа выбираются
 * в файловом менеджере. Тесты самого редактора — в его репозитории
 */
class TextEditorCest
{
    private const FORM = '/admin/content/page/?action=form&content_id=1';
    private const APPLY = '.edit-bar button[name="submit_apply"]';
    /** Редактор поля «Текст» страницы (FieldText, editorFull) */
    private const EDITOR = '.form-editor:has(textarea[name="text"]) .ted-ui';
    private const EDITABLE = self::EDITOR . ' .ted-ui-editable';
    private const FILE_MANAGER = '#sf-file-manager';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToOpenEditor(AcceptanceTester $I): void
    {
        $I->amOnPage(self::FORM);
        $I->dontSeeErrors();
        $I->waitForElementVisible(self::EDITABLE, 10);
        // textarea поля скрыта, вместо неё — редактор с набором блоков поля
        $I->dontSeeElement('textarea[name="text"]');
        $I->seeElement(self::EDITOR . ' .ted-ui-toolbar [data-act="media"]');
        $I->assertSame('full', $I->executeJS(
            'return Ted.get(document.querySelector(\'textarea[name="text"]\')).server.preset'
        ));
        $this->dontSeeBrowserErrors($I);
        $I->dontSeeLogProblems();
    }

    public function tryToSaveText(AcceptanceTester $I): void
    {
        $before = $I->grabRowsFromSql('SELECT text FROM content WHERE content_id = 1')[0]['text'];
        $value = 'Codecept texted ' . time();
        try {
            $I->amOnPage(self::FORM);
            $I->waitForElementVisible(self::EDITABLE, 10);
            $I->click(self::EDITABLE);
            $I->pressKey(self::EDITABLE, $value);
            // Ctrl+S редактора сохраняет форму как «Применить»: страница остаётся на форме записи
            $I->executeJS('window.__beforeSubmit = true;');
            // клавиатура драйвера, а не элемент: страница перезагружается, пока клавиша ещё нажата
            $I->executeInSelenium(fn(RemoteWebDriver $driver) => $driver->getKeyboard()
                ->pressKey(WebDriverKeys::CONTROL)->sendKeys('s')->releaseKey(WebDriverKeys::CONTROL));
            $I->waitForJS('return !window.__beforeSubmit && document.readyState === "complete";', 10);
            $I->waitForElementVisible(self::EDITABLE, 10);
            $I->dontSeeErrors();
            $I->seeInCurrentUrl('action=form&content_id=1');

            $text = $I->grabRowsFromSql('SELECT text FROM content WHERE content_id = 1')[0]['text'];
            $I->assertStringContainsString('<div class="ted-content"', $text);
            $I->assertStringContainsString('<p>' . $value . '</p>', $text);
            // после перезагрузки текст снова в редакторе
            $I->see($value, self::EDITABLE);
        } finally {
            $I->runSql('UPDATE content SET text = ? WHERE content_id = 1', [$before]);
        }
        $this->dontSeeBrowserErrors($I);
        $I->dontSeeLogProblems();
    }

    public function tryToCleanHtmlOnSave(AcceptanceTester $I): void
    {
        // браузеру не доверяем: HTML в обход редактора сервер очищает по набору поля
        $before = $I->grabRowsFromSql('SELECT text FROM content WHERE content_id = 1')[0]['text'];
        try {
            $I->amOnPage(self::FORM);
            $I->waitForElementVisible(self::EDITABLE, 10);
            $I->executeJS(<<<'JS'
const textarea = document.querySelector('textarea[name="text"]');
Ted.destroy(textarea);
textarea.value = '<p onclick="alert(1)">Codecept clean</p><script>alert(2)</script>'
    + '<p><a href="javascript:alert(3)">link</a></p>';
JS);
            $I->submitAndWaitForReload(self::APPLY, self::EDITABLE);

            $text = $I->grabRowsFromSql('SELECT text FROM content WHERE content_id = 1')[0]['text'];
            $I->assertStringContainsString('Codecept clean', $text);
            $I->assertStringNotContainsString('onclick', $text);
            $I->assertStringNotContainsString('<script', $text);
            $I->assertStringNotContainsString('javascript:', $text);
        } finally {
            $I->runSql('UPDATE content SET text = ? WHERE content_id = 1', [$before]);
        }
        $I->dontSeeLogProblems();
    }

    public function tryToInsertImageFromFileManager(AcceptanceTester $I): void
    {
        $before = $I->grabRowsFromSql('SELECT text FROM content WHERE content_id = 1')[0]['text'];
        $file = codecept_root_dir('public/uf/images/codecept-texted.png');
        copy(codecept_data_dir('upload.png'), $file);
        try {
            $I->amOnPage(self::FORM);
            $I->waitForElementVisible(self::EDITABLE, 10);
            // «Медиа» → «Изображение»: пустой блок с выбором «Из библиотеки» (файловый менеджер админки)
            $I->click(self::EDITOR . ' .ted-ui-toolbar [data-act="media"]');
            $I->waitForElementVisible('.ted-ui-pop [data-value="image"]', 5);
            $I->click('.ted-ui-pop [data-value="image"]');
            $I->waitForElementVisible(self::EDITOR . ' .ted-ui-media__drop', 5);
            $I->click(self::EDITOR . ' .ted-ui-media__drop');

            $I->waitForElementVisible(self::FILE_MANAGER . ' .file-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::FILE_MANAGER . ' .file-tree-row[data-fm-path="/images"]');
            $card = self::FILE_MANAGER . ' .file-card[data-fm-path="/images/codecept-texted.png"]';
            $I->waitForElementVisible($card, 5);
            $I->click($card);
            $I->click(self::FILE_MANAGER . ' .file-window__done');
            $I->waitForElementNotVisible(self::FILE_MANAGER, 5);

            // картинка из библиотеки — в блоке «Изображение» с адресом от корня сайта
            $I->waitForElement(self::EDITABLE . ' img[src="/uf/images/codecept-texted.png"]', 5);
            $I->submitAndWaitForReload(self::APPLY, self::EDITABLE);
            $text = $I->grabRowsFromSql('SELECT text FROM content WHERE content_id = 1')[0]['text'];
            $I->assertStringContainsString('src="/uf/images/codecept-texted.png"', $text);
            // файл из библиотеки не удаляется после сохранения
            $I->assertFileExists($file);
        } finally {
            $I->runSql('UPDATE content SET text = ? WHERE content_id = 1', [$before]);
            if (is_file($file)) {
                unlink($file);
            }
        }
        $this->dontSeeBrowserErrors($I);
        $I->dontSeeLogProblems();
    }

    public function tryToCancelFileManager(AcceptanceTester $I): void
    {
        // окно менеджера закрыли без выбора: редактор не ждёт ответа, «Из библиотеки» можно нажать снова
        $I->amOnPage(self::FORM);
        $I->waitForElementVisible(self::EDITABLE, 10);
        $I->click(self::EDITOR . ' .ted-ui-toolbar [data-act="media"]');
        $I->waitForElementVisible('.ted-ui-pop [data-value="image"]', 5);
        $I->click('.ted-ui-pop [data-value="image"]');
        $I->waitForElementVisible(self::EDITOR . ' .ted-ui-media__drop', 5);
        for ($i = 0; $i < 2; $i++) {
            $I->click(self::EDITOR . ' .ted-ui-media__drop');
            $I->waitForElementVisible(self::FILE_MANAGER, 10);
            $I->executeJS('window.fileManager.close()');
            $I->waitForElementNotVisible(self::FILE_MANAGER, 5);
        }
        $this->dontSeeBrowserErrors($I);
    }

    /**
     * В консоли браузера нет ошибок скриптов (SEVERE)
     */
    private function dontSeeBrowserErrors(AcceptanceTester $I): void
    {
        $errors = $I->executeInSelenium(fn(RemoteWebDriver $driver) => array_filter(
            $driver->manage()->getLog('browser'),
            fn(array $entry) => $entry['level'] === 'SEVERE'
        ));
        $I->assertSame([], array_values($errors));
    }
}
