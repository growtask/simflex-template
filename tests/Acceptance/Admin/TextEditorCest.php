<?php

namespace Tests\Acceptance\Admin;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverKeys;
use Tests\Support\AcceptanceTester;

/**
 * Визуальный редактор полей админки — texted (пакет simflex/texted, Plugins\Editor\Editor ядра): поле с editorFull
 * открывается редактором без ошибок, текст сохраняется в базу очищенным на сервере, медиа загружаются
 * с компьютера (/admin/text-editor-upload) или выбираются в файловом менеджере («Из библиотеки»).
 * Тесты самого редактора — в его репозитории
 */
class TextEditorCest
{
    private const FORM = '/admin/content/page/?action=form&content_id=1';
    private const APPLY = '.edit-bar button[name="submit_apply"]';
    /** Редактор поля «Текст» страницы (FieldText, editorFull) */
    private const EDITOR = '.form-editor:has(textarea[name="text"]) .ted-ui';
    private const EDITABLE = self::EDITOR . ' .ted-ui-editable';
    private const FILE_MANAGER = '#sf-file-manager';
    /** Ссылка «Из библиотеки» в зоне загрузки пустого блока медиа (загрузка с компьютера + файловый менеджер) */
    private const LIBRARY = self::EDITOR . ' .ted-ui-media__drop [data-act="media-library"]';
    private const UPLOAD = '/admin/text-editor-upload';

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
        // не картинка в той же папке: в блок «Изображение» её не выбрать (accept по виду блока)
        $document = codecept_root_dir('public/uf/images/codecept-texted.pdf');
        file_put_contents($document, '%PDF-1.4');
        try {
            $I->amOnPage(self::FORM);
            $I->waitForElementVisible(self::EDITABLE, 10);
            // «Медиа» → «Изображение»: пустой блок с выбором «Из библиотеки» (файловый менеджер админки)
            $I->click(self::EDITOR . ' .ted-ui-toolbar [data-act="media"]');
            $I->waitForElementVisible('.ted-ui-pop [data-value="image"]', 5);
            $I->click('.ted-ui-pop [data-value="image"]');
            $I->waitForElementVisible(self::LIBRARY, 5);

            // окно закрыли крестиком без выбора, открыли снова — выбор доходит до редактора
            $I->click(self::LIBRARY);
            $I->waitForElementVisible(self::FILE_MANAGER, 10);
            $I->click(self::FILE_MANAGER . ' [data-fm-close]');
            $I->waitForElementNotVisible(self::FILE_MANAGER, 5);
            $I->click(self::LIBRARY);

            $folder = self::FILE_MANAGER . ' .fm-tree-row[data-fm-path="/images"]';
            $I->waitForElementVisible($folder, 10);
            $I->click($folder);
            $card = self::FILE_MANAGER . ' .fm-card[data-fm-path="/images/codecept-texted.png"]';
            $I->waitForElementVisible($card, 15);
            $I->seeElement(self::FILE_MANAGER . ' .fm-card_disabled[data-fm-path="/images/codecept-texted.pdf"]');
            $I->click($card . ' .fm-card__check');
            $I->click(self::FILE_MANAGER . ' [data-fm-action="done"]');
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
            foreach ([$file, $document] as $created) {
                if (is_file($created)) {
                    unlink($created);
                }
            }
        }
        $this->dontSeeBrowserErrors($I);
        $I->dontSeeLogProblems();
    }

    public function tryToUploadImage(AcceptanceTester $I): void
    {
        // загрузка с компьютера: функция upload редактора (Ted.createUpload) → картинка в WebP в папке записи
        $I->amOnPage(self::FORM);
        $I->waitForElementVisible(self::EDITABLE, 10);
        $options = $I->executeJS(
            'const o = Ted.get(document.querySelector(\'textarea[name="text"]\')).options;'
            . ' return {source: o.mediaSource, page: o.mediaPageUrl, library: typeof o.pickFile};'
        );
        $I->assertEquals(['source' => 'direct', 'page' => '/content/1', 'library' => 'function'], $options);

        $folder = codecept_root_dir('public/uf/images/content/1');
        $before = is_dir($folder) ? scandir($folder) : [];
        $result = $I->executeAsyncJS(<<<'JS'
const [base64, done] = arguments;
const bytes = Uint8Array.from(atob(base64), (c) => c.charCodeAt(0));
const file = new File([bytes], 'Codecept картинка.png', {type: 'image/png'});
const editor = Ted.get(document.querySelector('textarea[name="text"]'));
editor.options.upload(file, {kind: 'image', pagePath: editor.options.mediaPageUrl})
    .then((asset) => done({url: asset.url}), (error) => done({error: error.message}));
JS, [base64_encode((string)file_get_contents(codecept_data_dir('upload.png')))]);
        $created = is_dir($folder) ? array_diff(scandir($folder), $before) : [];
        try {
            $I->assertArrayNotHasKey('error', $result);
            $I->assertMatchesRegularExpression('~^/uf/images/content/1/codecept-kartinka(-\d+)?\.webp$~', $result['url']);
            $I->assertFileExists(codecept_root_dir('public' . $result['url']));
        } finally {
            foreach ($created as $name) {
                unlink($folder . '/' . $name);
            }
        }
        $this->dontSeeBrowserErrors($I);
        $I->dontSeeLogProblems();
    }

    public function tryToRefuseUploadWithoutCsrf(AcceptanceTester $I): void
    {
        // без CSRF-токена сессии загрузка не принимается; неизвестный набор блоков — ошибка, просмотр — нет права
        $I->amOnPage(self::FORM);
        $statuses = $I->executeAsyncJS(<<<'JS'
const [url, done] = arguments;
const token = document.querySelector('meta[name="csrf-token"]').content;
const send = (query, headers) => fetch(url + query, {method: 'POST', headers, body: new FormData()})
    .then((response) => response.status);
Promise.all([
    send('?preset=full', {}),
    send('?preset=nope', {'X-CSRF-Token': token}),
    send('?preset=readonly', {'X-CSRF-Token': token}),
]).then(done);
JS, [self::UPLOAD]);
        $I->assertSame([403, 400, 403], $statuses);
    }

    public function tryToCancelFileManager(AcceptanceTester $I): void
    {
        // окно менеджера закрыли без выбора: редактор не ждёт ответа, «Из библиотеки» можно нажать снова
        $I->amOnPage(self::FORM);
        $I->waitForElementVisible(self::EDITABLE, 10);
        $I->click(self::EDITOR . ' .ted-ui-toolbar [data-act="media"]');
        $I->waitForElementVisible('.ted-ui-pop [data-value="image"]', 5);
        $I->click('.ted-ui-pop [data-value="image"]');
        $I->waitForElementVisible(self::LIBRARY, 5);
        for ($i = 0; $i < 2; $i++) {
            $I->click(self::LIBRARY);
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
