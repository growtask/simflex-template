<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Файловый менеджер админки (пакет simflex/file-manager): окно, папки, загрузка, выбор файла кнопкой
 * «Готово», переименование из меню, удаление с подтверждением. Работает в public/uf/images/codecept-fm
 */
class FileManagerCest
{
    private const WINDOW = '#sf-file-manager';
    private const FOLDER = '/images/codecept-fm';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToManageFiles(AcceptanceTester $I): void
    {
        $dir = codecept_root_dir('public/uf' . self::FOLDER);
        try {
            $I->amOnPage('/admin');
            $I->waitForJS('return typeof window.fileManager === "object"', 10);
            $I->executeJS('window.fileManager.open({onSelect: (files) => { window.__picked = files; }})');
            $I->waitForElementVisible(self::WINDOW . ' .file-tree-row[data-fm-path="/images"]', 10);
            $I->dontSee('.gitkeep', self::WINDOW);

            // новая папка в /images
            $I->click(self::WINDOW . ' .file-tree-row[data-fm-path="/images"]');
            $I->waitForElement(self::WINDOW . ' .file-window__crumb_current', 5);
            $I->see('images', self::WINDOW . ' .file-window__crumb_current');
            $I->click(self::WINDOW . ' .file-window__tools [data-fm-action="create"]');
            $I->waitForElementVisible('#sf-file-manager-create [data-fm-name]', 5);
            $I->fillField('#sf-file-manager-create [data-fm-name]', 'codecept-fm');
            $I->click('#sf-file-manager-create [data-fm-action="create-save"]');
            $I->waitForElementVisible(self::WINDOW . ' .file-card[data-fm-path="' . self::FOLDER . '"]', 5);
            $I->assertDirectoryExists($dir);

            // папка открывается двойным кликом; пустая — состояние «Папка пуста»
            $I->doubleClick(self::WINDOW . ' .file-card[data-fm-path="' . self::FOLDER . '"]');
            $I->waitForElementVisible(self::WINDOW . ' [data-fm-state]', 5);
            $I->see('Папка пуста', self::WINDOW);

            // загрузка: файл появляется выбранным, «Готово» отдаёт его
            $I->attachFile(self::WINDOW . ' [data-fm-input]', 'upload.png');
            $file = self::WINDOW . ' .file-card[data-fm-path="' . self::FOLDER . '/upload.png"]';
            $I->waitForElementVisible($file, 10);
            $I->seeElement($file . '.file-card_style_new.file-card_active');
            $I->see('upload.png', self::WINDOW . ' [data-fm-info-name]');
            $I->click(self::WINDOW . ' .file-window__done');
            $I->waitForElementNotVisible(self::WINDOW, 5);
            $picked = $I->executeJS('return window.__picked');
            $I->assertSame(self::FOLDER . '/upload.png', $picked[0]['path']);
            $I->assertSame('/uf' . self::FOLDER . '/upload.png', $picked[0]['url']);
            $I->assertTrue($picked[0]['isImage']);

            // переименование из контекстного меню
            $I->executeJS('window.fileManager.open()');
            $I->waitForElementVisible($file, 10);
            $I->clickWithRightButton($file);
            $I->waitForElementVisible(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]', 5);
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]');
            $I->waitForElementVisible('#sf-file-manager-rename [data-fm-name]', 5);
            $I->seeInField('#sf-file-manager-rename [data-fm-name]', 'upload.png');
            $I->fillField('#sf-file-manager-rename [data-fm-name]', 'renamed.png');
            $I->click('#sf-file-manager-rename [data-fm-action="rename-save"]');
            $renamed = self::WINDOW . ' .file-card[data-fm-path="' . self::FOLDER . '/renamed.png"]';
            $I->waitForElementVisible($renamed, 5);
            $I->assertFileExists($dir . '/renamed.png');

            // недопустимое имя — ошибка, файл не меняется
            $I->clickWithRightButton($renamed);
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]');
            $I->waitForElementVisible('#sf-file-manager-rename [data-fm-name]', 5);
            $I->fillField('#sf-file-manager-rename [data-fm-name]', 'evil.php');
            $I->click('#sf-file-manager-rename [data-fm-action="rename-save"]');
            $I->waitForText('Недопустимое имя', 5);
            $I->click('#sf-file-manager-rename [data-modal-close]');
            $I->assertFileExists($dir . '/renamed.png');

            // удаление папки — после подтверждения, окно возвращается в /images
            $I->click(self::WINDOW . ' .file-window__tools [data-fm-action="delete-folder"]');
            $I->waitForElementVisible('#sf-modal-confirm [data-sf-confirm="yes"]', 5);
            $I->see('Удалить папку?', '#sf-modal-confirm');
            $I->click('#sf-modal-confirm [data-sf-confirm="yes"]');
            // крошки и список перерисовываются: ждём скриптом, без ссылок на элементы
            $I->waitForJS(
                'return document.querySelector("#sf-file-manager .file-window__crumb_current")?.textContent === "images"'
                . ' && !document.querySelector(\'#sf-file-manager .file-card[data-fm-path="' . self::FOLDER . '"]\')',
                5
            );
            $I->assertDirectoryDoesNotExist($dir);
        } finally {
            foreach (glob($dir . '/*') ?: [] as $path) {
                unlink($path);
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
        $I->dontSeeLogProblems();
    }

    public function tryToGetApiEnvelope(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin');
        $fetch = fn(string $url, array $options = []): array => $I->executeAsyncJS(
            'const done = arguments[arguments.length - 1];
            fetch(arguments[0], arguments[1]).then(r => r.json().then(json => done({status: r.status, json})));',
            [$url, $options]
        );

        $tree = $fetch('/admin/fm?method=getTree');
        $I->assertSame(200, $tree['status']);
        $I->assertSame(0, $tree['json']['error']['code']);
        $I->assertContains('/images', array_column($tree['json']['items'], 'path'));

        $unknown = $fetch('/admin/fm?method=nope');
        $I->assertSame(404, $unknown['status']);
        $I->assertSame('Неизвестное действие', $unknown['json']['error']['message']);

        $missing = $fetch('/admin/fm?method=getFileList&path=/codecept-no-dir');
        $I->assertSame(404, $missing['status']);

        $badName = $fetch('/admin/fm?method=createDir&path=/images&name=.evil', ['method' => 'POST']);
        $I->assertSame(422, $badName['status']);
        $I->assertSame('Недопустимое имя', $badName['json']['error']['message']);
        $I->assertDirectoryDoesNotExist(codecept_root_dir('public/uf/images/.evil'));

        $notFound = $fetch('/admin/fm?method=delete&path=/codecept-no-file', ['method' => 'POST']);
        $I->assertSame(404, $notFound['status']);
        $I->assertSame(404, $notFound['json']['error']['code']);
    }

    public function tryToPickFileForTableRow(AcceptanceTester $I): void
    {
        // картинка колонки поля-таблицы «Из библиотеки»: менеджер — поверх окна строки, после выбора окно строки
        // остаётся открытым с адресом файла
        $file = codecept_root_dir('public/uf/images/codecept-pick.png');
        copy(codecept_data_dir('upload.png'), $file);
        try {
            $I->amOnPage('/admin/content/page/?action=form&content_id=1');
            $slides = '[data-sf="table-editor"]:has([data-sf-table-value][name="slides"])';
            $I->waitForElement($slides, 10);
            $modal = '#' . $I->executeJS('return document.querySelector(arguments[0]).dataset.sfTableModal', [$slides]);
            $I->executeJS('document.querySelector(arguments[0]).click()', [$slides . ' [data-sf-table-add]']);
            $I->waitForElementVisible($modal . ' [data-sf-table-library]', 5);
            $I->click($modal . ' [data-sf-table-library]');
            $I->waitForElementVisible(self::WINDOW . ' .file-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .file-tree-row[data-fm-path="/images"]');
            $card = self::WINDOW . ' .file-card[data-fm-path="/images/codecept-pick.png"]';
            $I->waitForElementVisible($card, 5);
            $I->click($card);
            $I->click(self::WINDOW . ' .file-window__done');
            $I->waitForElementNotVisible(self::WINDOW, 5);
            $I->seeElement($modal . ' .edit-table-file__preview[src="/uf/images/codecept-pick.png"]');
            $I->assertSame('/uf/images/codecept-pick.png', $I->executeJS(
                'return document.querySelector(arguments[0]).value',
                [$modal . ' [data-sf-column="image"]']
            ));
            // окно строки доступно: не осталось под inert
            $I->assertFalse($I->executeJS('return document.querySelector(arguments[0]).hasAttribute("inert")', [$modal]));
        } finally {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function tryToInsertImageIntoEditor(AcceptanceTester $I): void
    {
        // кнопка «Файлы» TinyMCE (плагин fm): картинка вставляется тегом img с адресом от корня сайта
        $file = codecept_root_dir('public/uf/images/codecept-pick.png');
        copy(codecept_data_dir('upload.png'), $file);
        try {
            $I->amOnPage('/admin/content/page/?action=form&content_id=1');
            $I->waitForElement('.tox-tbtn[aria-label="Файлы"]', 10);
            $I->executeJS('document.querySelector(\'.tox-tbtn[aria-label="Файлы"]\').click()');
            $I->waitForElementVisible(self::WINDOW . ' .file-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .file-tree-row[data-fm-path="/images"]');
            $card = self::WINDOW . ' .file-card[data-fm-path="/images/codecept-pick.png"]';
            $I->waitForElementVisible($card, 5);
            $I->doubleClick($card);
            $I->waitForElementNotVisible(self::WINDOW, 5);
            $I->assertStringContainsString(
                '<img src="/uf/images/codecept-pick.png"',
                $I->executeJS('return tinymce.get()[0].getContent()')
            );
        } finally {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
