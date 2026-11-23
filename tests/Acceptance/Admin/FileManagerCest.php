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
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->dontSee('.gitkeep', self::WINDOW);

            // новая папка в /images
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $I->waitForElement(self::WINDOW . ' .fm-crumbs__item[aria-current="page"]', 5);
            $I->see('images', self::WINDOW . ' .fm-crumbs__item[aria-current="page"]');
            $I->click(self::WINDOW . ' .fm-tools [data-fm-action="create"]');
            $I->waitForElementVisible('#sf-file-manager-create [data-fm-name]', 5);
            $I->fillField('#sf-file-manager-create [data-fm-name]', 'codecept-fm');
            $I->click('#sf-file-manager-create [data-fm-action="create-save"]');
            $I->waitForElementVisible(self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '"]', 5);
            $I->assertDirectoryExists($dir);

            // папка открывается кликом; пустая — сначала загрузка, потом «В папке пока нет файлов»
            $I->click(self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '"]');
            $I->waitForText('В папке пока нет файлов', 10, self::WINDOW);

            // загрузка: файл появляется выбранным, «Готово» отдаёт его
            $I->attachFile(self::WINDOW . ' [data-fm-input]', 'upload.png');
            $file = self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '/upload.webp"]';
            $I->waitForElementVisible($file, 10);
            $I->seeElement($file . '.fm-card_new[aria-selected="true"]');
            $I->see('upload.webp', self::WINDOW . ' [data-fm-info-name]');
            $I->click(self::WINDOW . ' .fm-done');
            $I->waitForElementNotVisible(self::WINDOW, 5);
            $picked = $I->executeJS('return window.__picked');
            $I->assertSame(self::FOLDER . '/upload.webp', $picked[0]['path']);
            $I->assertSame('/uf' . self::FOLDER . '/upload.webp', $picked[0]['url']);
            $I->assertTrue($picked[0]['isImage']);

            // переименование из контекстного меню
            $I->executeJS('window.fileManager.open()');
            $I->waitForElementVisible($file, 10);
            $I->clickWithRightButton($file);
            $I->waitForElementVisible(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]', 5);
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]');
            $I->waitForElementVisible('#sf-file-manager-rename [data-fm-name]', 5);
            $I->seeInField('#sf-file-manager-rename [data-fm-name]', 'upload.webp');
            $I->fillField('#sf-file-manager-rename [data-fm-name]', 'renamed.webp');
            $I->click('#sf-file-manager-rename [data-fm-action="rename-save"]');
            $renamed = self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '/renamed.webp"]';
            $I->waitForElementVisible($renamed, 5);
            $I->assertFileExists($dir . '/renamed.webp');

            // недопустимое имя — ошибка, файл не меняется
            $I->clickWithRightButton($renamed);
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]');
            $I->waitForElementVisible('#sf-file-manager-rename [data-fm-name]', 5);
            $I->fillField('#sf-file-manager-rename [data-fm-name]', 'evil.php');
            $I->click('#sf-file-manager-rename [data-fm-action="rename-save"]');
            $I->waitForText('Недопустимое имя', 5);
            $I->click('#sf-file-manager-rename [data-fm-close]');
            $I->assertFileExists($dir . '/renamed.webp');

            // удаление папки — после подтверждения, окно возвращается в /images
            $I->click(self::WINDOW . ' .fm-tools [data-fm-action="delete-folder"]');
            $I->waitForElementVisible('#sf-file-manager-confirm [data-fm-action="confirm-yes"]', 5);
            $I->see('Удалить папку?', '#sf-file-manager-confirm');
            $I->click('#sf-file-manager-confirm [data-fm-action="confirm-yes"]');
            // крошки и список перерисовываются: ждём скриптом, без ссылок на элементы
            $I->waitForJS(
                'return document.querySelector("#sf-file-manager .fm-crumbs__item[aria-current=page]")?.textContent === "images"'
                . ' && !document.querySelector(\'#sf-file-manager .fm-card[data-fm-path="' . self::FOLDER . '"]\')',
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

    public function tryToCancelPicking(AcceptanceTester $I): void
    {
        // закрытие без выбора — onCancel и null в Promise open(); ответ на одно открытие ровно один
        $I->amOnPage('/admin');
        $I->waitForJS('return typeof window.fileManager === "object"', 10);
        $I->executeJS('window.__fm = [];
            window.__open = (name) => window.fileManager.open({
                onSelect: () => window.__fm.push(name + ":select"),
                onCancel: () => window.__fm.push(name + ":cancel"),
            }).then((files) => window.__fm.push(name + ":" + JSON.stringify(files)));');
        $answers = fn(): array => $I->executeJS('return window.__fm');
        $waitAnswers = fn(int $count) => $I->waitForJS('return window.__fm.length === ' . $count, 5);

        // Esc
        $I->executeJS('window.__open("esc")');
        $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
        $I->pressKey(self::WINDOW . ' [data-fm-search]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible(self::WINDOW, 5);
        $waitAnswers(2);
        $I->assertSame(['esc:cancel', 'esc:null'], $answers());

        // крестик
        $I->executeJS('window.__open("close")');
        $I->waitForElementVisible(self::WINDOW . ' .fm-window__head [data-fm-close]', 5);
        $I->click(self::WINDOW . ' .fm-window__head [data-fm-close]');
        $I->waitForElementNotVisible(self::WINDOW, 5);
        $waitAnswers(4);

        // подложка
        $I->executeJS('window.__open("overlay")');
        $I->waitForElementVisible(self::WINDOW . ' .fm-window__head [data-fm-close]', 5);
        $I->executeJS('document.querySelector(arguments[0]).dispatchEvent(new MouseEvent("click", {bubbles: true}))', [self::WINDOW]);
        $I->waitForElementNotVisible(self::WINDOW, 5);
        $waitAnswers(6);

        // новый open() при открытом окне: прежний получает отмену, окно остаётся открытым; close() — отмена нового
        $I->executeJS('window.__open("first")');
        $I->waitForElementVisible(self::WINDOW . ' .fm-window__head [data-fm-close]', 5);
        $I->executeJS('window.__open("second")');
        $waitAnswers(8);
        $I->seeElement(self::WINDOW . '.is-open');
        $I->executeJS('window.fileManager.close()');
        $waitAnswers(10);
        $I->assertSame([
            'esc:cancel', 'esc:null',
            'close:cancel', 'close:null',
            'overlay:cancel', 'overlay:null',
            'first:cancel', 'first:null',
            'second:cancel', 'second:null',
        ], $answers());
    }

    public function tryToPickOnlyAcceptedFiles(AcceptanceTester $I): void
    {
        // accept: неподходящий файл виден, но не выбирается; загрузить через окно можно только подходящий
        $dir = codecept_root_dir('public/uf' . self::FOLDER);
        $pdf = codecept_data_dir('codecept-fm.pdf');
        mkdir($dir, 0777, true);
        copy(codecept_data_dir('upload.png'), $dir . '/photo.png');
        file_put_contents($dir . '/doc.pdf', '%PDF-1.4');
        file_put_contents($pdf, '%PDF-1.4');
        try {
            $I->amOnPage('/admin');
            $I->waitForJS('return typeof window.fileManager === "object"', 10);
            $I->executeJS('window.fileManager.open({select: true, accept: ["jpg", "png"]})'
                . '.then((files) => { window.__picked = files; })');
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->executeJS('document.querySelector(arguments[0]).click()', [self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]']);
            $folder = self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '"]';
            $I->waitForElementVisible($folder, 5);
            $I->dontSeeElement($folder . '.fm-card_disabled');
            $I->click($folder);

            $doc = self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '/doc.pdf"]';
            $photo = self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '/photo.png"]';
            $I->waitForElementVisible($doc, 5);
            $I->seeElement($doc . '.fm-card_disabled[aria-disabled="true"]');
            $I->dontSeeElement($photo . '.fm-card_disabled');

            // pdf не выбирается ни кликом, ни двойным кликом
            $I->click($doc);
            $I->dontSeeElement($doc . '[aria-selected="true"]');
            $I->seeElement(self::WINDOW . ' .fm-done[disabled]');
            $I->doubleClick($doc);
            $I->seeElement(self::WINDOW . '.is-open');

            // неподходящий файл не загружается
            $I->assertSame('.jpg,.png', $I->executeJS('return document.querySelector(arguments[0]).accept', [self::WINDOW . ' [data-fm-input]']));
            $I->attachFile(self::WINDOW . ' [data-fm-input]', 'codecept-fm.pdf');
            $I->waitForText('Не загружено: codecept-fm.pdf', 5);
            $I->assertFileDoesNotExist($dir . '/codecept-fm.pdf');

            // картинка выбирается, Promise получает её
            $I->click($photo);
            $I->seeElement($photo . '[aria-selected="true"]');
            $I->click(self::WINDOW . ' .fm-done');
            $I->waitForElementNotVisible(self::WINDOW, 5);
            $I->waitForJS('return Array.isArray(window.__picked)', 5);
            $picked = $I->executeJS('return window.__picked');
            $I->assertSame(['/uf' . self::FOLDER . '/photo.png'], array_column($picked, 'url'));
        } finally {
            foreach (glob($dir . '/*') ?: [] as $path) {
                unlink($path);
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
            if (is_file($pdf)) {
                unlink($pdf);
            }
        }
    }

    public function tryToConfirmAboveWindow(AcceptanceTester $I): void
    {
        // окна менеджера — свои (<dialog>): подтверждение поверх окна менеджера, Esc закрывает только верхнее
        $I->amOnPage('/admin');
        $I->waitForJS('return typeof window.fileManager === "object"', 10);
        $I->executeJS('window.fileManager.open()');
        $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
        $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
        $I->waitForElement(self::WINDOW . ' .fm-crumbs__item[aria-current="page"]', 5);
        $I->click(self::WINDOW . ' .fm-tools [data-fm-action="delete-folder"]');
        $confirm = '#sf-file-manager-confirm';
        $I->waitForElementVisible($confirm . ' [data-fm-action="confirm-yes"]', 5);
        $I->see('Удалить папку?', $confirm);
        $I->assertTrue($I->executeJS(
            'const box = document.querySelector("#sf-file-manager-confirm .fm-modal__box").getBoundingClientRect();
            const top = document.elementFromPoint(box.left + box.width / 2, box.top + box.height / 2);
            return top.closest("#sf-file-manager-confirm") !== null;'
        ));
        $I->pressKey($confirm . ' [data-fm-action="confirm-yes"]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible($confirm, 5);
        $I->seeElement(self::WINDOW . '.is-open');
        $I->assertDirectoryExists(codecept_root_dir('public/uf/images'));
        $I->pressKey(self::WINDOW . ' [data-fm-search]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible(self::WINDOW, 5);
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

        // дерево по уровням: depth=1 — только вложенные папки, есть ли у них свои — has_children
        $level = $fetch('/admin/fm?method=getTree&path=/images&depth=1');
        $I->assertSame(200, $level['status']);
        foreach ($level['json']['items'] as $node) {
            $I->assertNull($node['data']);
            $I->assertIsBool($node['has_children']);
            $I->assertStringStartsWith('/images/', $node['path']);
        }

        $unknown = $fetch('/admin/fm?method=nope');
        $I->assertSame(404, $unknown['status']);
        $I->assertSame('Неизвестное действие', $unknown['json']['error']['message']);

        $missing = $fetch('/admin/fm?method=getFileList&path=/codecept-no-dir');
        $I->assertSame(404, $missing['status']);

        // изменяющий запрос — POST с CSRF-токеном админки
        $token = $I->executeJS('return document.querySelector(\'meta[name="csrf-token"]\').content');
        $post = ['method' => 'POST', 'headers' => ['X-CSRF-Token' => $token]];

        $badName = $fetch('/admin/fm?method=createDir&path=/images&name=.evil', $post);
        $I->assertSame(422, $badName['status']);
        $I->assertSame('Недопустимое имя', $badName['json']['error']['message']);
        $I->assertDirectoryDoesNotExist(codecept_root_dir('public/uf/images/.evil'));

        $notFound = $fetch('/admin/fm?method=delete&path=/codecept-no-file', $post);
        $I->assertSame(404, $notFound['status']);
        $I->assertSame(404, $notFound['json']['error']['code']);
    }

    public function tryToRejectForgedAndUnsafeRequests(AcceptanceTester $I): void
    {
        // GET (картинка или ссылка в тексте страницы) и POST без CSRF-токена файлы не меняют; корень, соседние
        // с uf папки и выход за корень недоступны
        $dir = codecept_root_dir('public/uf/images/codecept-fm-safe');
        $sibling = codecept_root_dir('public/uf_codecept');
        mkdir($dir, 0777, true);
        mkdir($sibling, 0777, true);
        try {
            $I->amOnPage('/admin');
            $fetch = fn(string $url, array $options = []): array => $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1];
                fetch(arguments[0], arguments[1]).then(r => r.json().then(json => done({status: r.status, json})));',
                [$url, $options]
            );
            $token = $I->executeJS('return document.querySelector(\'meta[name="csrf-token"]\').content');
            $post = ['method' => 'POST', 'headers' => ['X-CSRF-Token' => $token]];
            $target = '&path=/images/codecept-fm-safe&name=codecept-fm-moved';

            foreach (['rename' . $target, 'delete&path=/images/codecept-fm-safe', 'createDir&path=/images&name=x'] as $query) {
                $I->assertSame(405, $fetch('/admin/fm?method=' . $query)['status'], $query);
            }
            // картинка в тексте шлёт GET с cookies — ничего не меняется
            $I->executeAsyncJS('const done = arguments[arguments.length - 1]; const image = new Image();
                image.onload = image.onerror = () => done(true); image.src = arguments[0];',
                ['/admin/fm?method=rename' . $target]);
            $I->assertSame(403, $fetch('/admin/fm?method=rename' . $target, ['method' => 'POST'])['status']);
            $I->assertSame(403, $fetch('/admin/fm?method=rename' . $target, ['method' => 'POST', 'headers' => ['X-CSRF-Token' => 'x']])['status']);
            $I->assertDirectoryExists($dir);

            // корень не переименовывается и не удаляется
            foreach (['rename&path=&name=uf-x', 'rename&path=/&name=uf-x', 'delete&path=', 'delete&path=/', 'delete&path=/.'] as $query) {
                $I->assertSame(400, $fetch('/admin/fm?method=' . $query, $post)['status'], $query);
            }
            $I->assertDirectoryExists(codecept_root_dir('public/uf/images'));

            // соседняя с uf папка (uf_codecept) и выход за корень
            foreach (['getFileList&path=_codecept', 'getFileList&path=/../', 'delete&path=/images/../../uf_codecept'] as $query) {
                $options = str_starts_with($query, 'delete') ? $post : [];
                $I->assertSame(400, $fetch('/admin/fm?method=' . $query, $options)['status'], $query);
            }
            $I->assertDirectoryExists($sibling);
        } finally {
            foreach ([$dir, codecept_root_dir('public/uf/images/codecept-fm-moved'), $sibling] as $path) {
                if (is_dir($path)) {
                    rmdir($path);
                }
            }
        }
    }

    public function tryToWarnAboutUsage(AcceptanceTester $I): void
    {
        // файл стоит в записи: API usage находит таблицу, подтверждение удаления предупреждает
        $file = codecept_root_dir('public/uf/images/codecept-usage.png');
        copy(codecept_data_dir('upload.png'), $file);
        $I->runSql('CREATE TABLE codecept_usage (id INT PRIMARY KEY, body TEXT)');
        $I->runSql('INSERT INTO codecept_usage VALUES (1, ?)', ['<p><img src="/uf/images/codecept-usage.png" alt=""></p>']);
        try {
            $I->amOnPage('/admin');
            $usage = $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1];
                fetch(arguments[0]).then(r => r.json()).then(json => done(json.items ?? []));',
                ['/admin/fm?method=usage&path=/images/codecept-usage.png']
            );
            $I->assertEquals([['table' => 'codecept_usage', 'count' => 1]], $usage);
            $free = $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1];
                fetch(arguments[0]).then(r => r.json()).then(json => done(json.items ?? []));',
                ['/admin/fm?method=usage&path=/images']
            );
            $I->assertTrue(in_array(['table' => 'codecept_usage', 'count' => 1], $free), 'ссылка на файл папки — ссылка на папку');

            $I->executeJS('window.fileManager.open()');
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $card = self::WINDOW . ' .fm-card[data-fm-path="/images/codecept-usage.png"]';
            $I->waitForElementVisible($card, 10);
            $I->clickWithRightButton($card);
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="delete"]');
            $I->waitForText('Ссылки на файл есть в 1 записи (codecept_usage)', 10, '#sf-file-manager-confirm');
            $I->click('#sf-file-manager-confirm [data-fm-close].fm-button');
            $I->waitForElementNotVisible('#sf-file-manager-confirm', 5);
            $I->assertFileExists($file);
        } finally {
            $I->runSql('DROP TABLE IF EXISTS codecept_usage');
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    public function tryToUploadWithoutOverwrite(AcceptanceTester $I): void
    {
        // загрузка: имя латиницей, PNG сжимается в WebP (не больше 2560 px), занятое имя не перезаписывается
        $dir = codecept_root_dir('public/uf/images/codecept-fm-upload');
        mkdir($dir, 0777, true);
        // тест и веб-сервер — разные пользователи: папка доступна на запись сайту (mkdir урезает права по umask)
        chmod($dir, 0777);
        try {
            $I->amOnPage('/admin');
            // сжатие большой картинки на сервере — дольше обычного ожидания скрипта
            $I->executeInSelenium(fn($driver) => $driver->manage()->timeouts()->setScriptTimeout(60));
            $upload = fn(): array => $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1];
                const canvas = document.createElement("canvas");
                canvas.width = 3000;
                canvas.height = 1000;
                canvas.getContext("2d").fillRect(0, 0, 10, 10);
                canvas.toBlob((blob) => {
                    const body = new FormData();
                    body.append("file[]", new File([blob], "Фото Отпуска.PNG", {type: "image/png"}));
                    fetch("/admin/fm?method=upload&path=/images/codecept-fm-upload", {method: "POST", body,
                        headers: {"X-CSRF-Token": document.querySelector(\'meta[name="csrf-token"]\').content}})
                        .then(r => r.json()).then(json => done(json.items));
                }, "image/png");'
            );

            $I->assertSame(['/images/codecept-fm-upload/foto-otpuska.webp'], $upload());
            $I->assertSame(['/images/codecept-fm-upload/foto-otpuska-2.webp'], $upload());
            $size = getimagesize($dir . '/foto-otpuska.webp');
            $I->assertSame([2560, 853, IMAGETYPE_WEBP], [$size[0], $size[1], $size[2]]);
        } finally {
            foreach (glob($dir . '/*') ?: [] as $path) {
                unlink($path);
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
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
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $card = self::WINDOW . ' .fm-card[data-fm-path="/images/codecept-pick.png"]';
            $I->waitForElementVisible($card, 5);
            $I->click($card);
            $I->click(self::WINDOW . ' .fm-done');
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
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $card = self::WINDOW . ' .fm-card[data-fm-path="/images/codecept-pick.png"]';
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
