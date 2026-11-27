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
            // картинку можно сжать — окно спрашивает; ответ «Сжать»
            $I->executeJS('sessionStorage.removeItem("sf-file-manager-compress")');
            $I->attachFile(self::WINDOW . ' [data-fm-input]', 'upload.png');
            $I->waitForElementVisible('#sf-file-manager-compress [data-fm-action="compress-yes"]', 10);
            $I->see('Картинки (1)', '#sf-file-manager-compress');
            $I->click('#sf-file-manager-compress [data-fm-action="compress-yes"]');
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
            $I->waitForElement('#sf-file-manager-rename [data-fm-action="rename-save"]:not([disabled])', 10);
            $I->click('#sf-file-manager-rename [data-fm-action="rename-save"]');
            $renamed = self::WINDOW . ' .fm-card[data-fm-path="' . self::FOLDER . '/renamed.webp"]';
            $I->waitForElementVisible($renamed, 5);
            $I->assertFileExists($dir . '/renamed.webp');

            // недопустимое имя — ошибка, файл не меняется
            $I->clickWithRightButton($renamed);
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="rename"]');
            $I->waitForElementVisible('#sf-file-manager-rename [data-fm-name]', 5);
            $I->fillField('#sf-file-manager-rename [data-fm-name]', 'evil.php');
            $I->waitForElement('#sf-file-manager-rename [data-fm-action="rename-save"]:not([disabled])', 10);
            $I->click('#sf-file-manager-rename [data-fm-action="rename-save"]');
            $I->waitForText('Недопустимое имя', 5);
            $I->click('#sf-file-manager-rename [data-fm-close]');
            $I->assertFileExists($dir . '/renamed.webp');

            // удаление папки — после подтверждения, окно возвращается в /images
            $I->click(self::WINDOW . ' .fm-tools [data-fm-action="delete-folder"]');
            $I->waitForElementVisible('#sf-file-manager-confirm [data-fm-action="confirm-yes"]', 5);
            $I->see('Удалить папку?', '#sf-file-manager-confirm');
            $I->see('Папка «codecept-fm»', '#sf-file-manager-confirm');
            $I->waitForElement('#sf-file-manager-confirm [data-fm-action="confirm-yes"]:not([disabled])', 10);
            $I->click('#sf-file-manager-confirm [data-fm-action="confirm-yes"]');
            // крошки и список перерисовываются: ждём скриптом, без ссылок на элементы
            $I->waitForJS(
                'return document.querySelector("#sf-file-manager .fm-crumbs__item[aria-current=page]")'
                . '?.textContent === "images"'
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
        $I->executeJS(
            'document.querySelector(arguments[0]).dispatchEvent(new MouseEvent("click", {bubbles: true}))',
            [self::WINDOW]
        );
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
            $I->executeJS(
                'document.querySelector(arguments[0]).click()',
                [self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]']
            );
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
            $I->assertSame('.jpg,.png', $I->executeJS(
                'return document.querySelector(arguments[0]).accept',
                [self::WINDOW . ' [data-fm-input]']
            ));
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
        $dir = codecept_root_dir('public/uf/images/codecept-fm-confirm');
        mkdir($dir, 0777, true);
        chmod($dir, 0777);
        $I->amOnPage('/admin');
        $I->waitForJS('return typeof window.fileManager === "object"', 10);
        $I->executeJS('window.fileManager.open()');
        $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
        $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
        $folder = self::WINDOW . ' .fm-card[data-fm-path="/images/codecept-fm-confirm"]';
        $I->waitForElementVisible($folder, 10);
        $I->click($folder);
        $I->waitForElement(self::WINDOW . ' .fm-tools [data-fm-action="delete-folder"]:not([disabled])', 10);
        $I->click(self::WINDOW . ' .fm-tools [data-fm-action="delete-folder"]');
        $confirm = '#sf-file-manager-confirm';
        $I->waitForElementVisible($confirm . ' [data-fm-action="confirm-yes"]', 5);
        $I->see('Удалить папку?', $confirm);
        $I->assertTrue($I->executeJS(
            'const box = document.querySelector("#sf-file-manager-confirm .fm-modal__box").getBoundingClientRect();
            const top = document.elementFromPoint(box.left + box.width / 2, box.top + box.height / 2);
            return top.closest("#sf-file-manager-confirm") !== null;'
        ));
        // «Удалить» недоступна, пока ищутся ссылки: Esc — на «Отмена»
        $I->pressKey($confirm . ' .fm-modal__footer [data-fm-close]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible($confirm, 5);
        $I->seeElement(self::WINDOW . '.is-open');
        $I->assertDirectoryExists($dir);
        $I->pressKey(self::WINDOW . ' [data-fm-search]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
        $I->waitForElementNotVisible(self::WINDOW, 5);
        rmdir($dir);
    }

    public function tryToDeleteOnlyWhatIsShown(AcceptanceTester $I): void
    {
        // лишнее не удаляется: системные папки (images, files, video, audio) не удаляются, не переименовываются
        // и не перемещаются; Delete на карточке без отметки удаляет её, а не выбранную раньше; подтверждение
        // называет, что удалится, и «Удалить» доступна только после проверки ссылок
        $dir = codecept_root_dir('public/uf/images/codecept-fm-delete');
        mkdir($dir, 0777, true);
        chmod($dir, 0777);
        foreach (['a', 'b'] as $name) {
            copy(codecept_data_dir('upload.png'), $dir . '/' . $name . '.png');
        }
        $confirm = '#sf-file-manager-confirm';
        try {
            $I->amOnPage('/admin');
            $I->waitForJS('return typeof window.fileManager === "object"', 10);

            // API: системные папки — 403, папки на месте
            $token = $I->executeJS('return document.querySelector(\'meta[name="csrf-token"]\').content');
            $status = fn(string $query, array $items = []) => $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1]; const body = new FormData();
                arguments[1].forEach((item) => body.append("items[]", item));
                const headers = {"X-CSRF-Token": arguments[2]};
                fetch("/admin/fm?method=" + arguments[0], {method: "POST", body, headers})
                    .then((r) => done(r.status));',
                [$query, $items, $token]
            );
            $I->assertSame(403, $status('delete&path=/images'));
            $I->assertSame(403, $status('rename&path=/files&name=files-x'));
            $I->assertSame(403, $status('move&path=/files', ['/images']));
            $I->assertDirectoryExists(codecept_root_dir('public/uf/images'));
            $I->assertDirectoryExists(codecept_root_dir('public/uf/files'));

            // окно: у системной папки в меню нет «Удалить», «Удалить папку» внизу недоступна
            $I->executeJS('window.fileManager.open()');
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->clickWithRightButton(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $I->waitForElementVisible(self::WINDOW . ' [data-fm-menu="folder"] [data-fm-action="upload-to"]', 5);
            $I->dontSeeElement(self::WINDOW . ' [data-fm-menu="folder"] [data-fm-action="delete"]');
            $I->dontSeeElement(self::WINDOW . ' [data-fm-menu="folder"] [data-fm-action="rename"]');
            $I->pressKey(self::WINDOW . ' [data-fm-search]', \Facebook\WebDriver\WebDriverKeys::ESCAPE);
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $I->waitForElement(self::WINDOW . ' .fm-tools [data-fm-action="delete-folder"][disabled]', 10);

            // a.png выбран, Delete нажат на b.png — удаляется только b.png, после подтверждения
            $folder = self::WINDOW . ' .fm-card[data-fm-path="/images/codecept-fm-delete"]';
            $I->waitForElementVisible($folder, 10);
            $I->click($folder);
            $card = fn(string $name) => self::WINDOW
                . ' .fm-card[data-fm-path="/images/codecept-fm-delete/' . $name . '"]';
            $I->waitForElementVisible($card('b.png'), 10);
            $I->click($card('a.png'));
            $I->executeJS('document.querySelector(arguments[0]).focus()', [$card('b.png')]);
            $I->pressKey($card('b.png'), \Facebook\WebDriver\WebDriverKeys::DELETE);
            $I->waitForElementVisible($confirm, 5);
            $I->see('Файл «b.png» будет удалён сразу.', $confirm);
            $I->waitForElement($confirm . ' [data-fm-action="confirm-yes"]:not([disabled])', 10);
            $I->click($confirm . ' [data-fm-action="confirm-yes"]');
            $I->waitForElementNotVisible($card('b.png'), 10);
            $I->assertFileDoesNotExist($dir . '/b.png');
            $I->assertFileExists($dir . '/a.png');
            $I->executeJS('window.fileManager.close()');
        } finally {
            foreach (glob($dir . '/*') ?: [] as $path) {
                unlink($path);
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
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
        // с uf папки, выход за корень и символическая ссылка наружу недоступны
        $dir = codecept_root_dir('public/uf/images/codecept-fm-safe');
        $sibling = codecept_root_dir('public/uf_codecept');
        $link = codecept_root_dir('public/uf/images/codecept-fm-link');
        mkdir($dir, 0777, true);
        mkdir($sibling, 0777, true);
        symlink($sibling, $link);
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

            $getQueries = ['rename' . $target, 'delete&path=/images/codecept-fm-safe', 'createDir&path=/images&name=x'];
            foreach ($getQueries as $query) {
                $I->assertSame(405, $fetch('/admin/fm?method=' . $query)['status'], $query);
            }
            // картинка в тексте шлёт GET с cookies — ничего не меняется
            $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1]; const image = new Image();
                image.onload = image.onerror = () => done(true); image.src = arguments[0];',
                ['/admin/fm?method=rename' . $target]
            );
            $I->assertSame(403, $fetch('/admin/fm?method=rename' . $target, ['method' => 'POST'])['status']);
            $badToken = ['method' => 'POST', 'headers' => ['X-CSRF-Token' => 'x']];
            $I->assertSame(403, $fetch('/admin/fm?method=rename' . $target, $badToken)['status']);
            $I->assertDirectoryExists($dir);

            // корень не переименовывается и не удаляется
            $rootQueries = ['rename&path=&name=uf-x', 'rename&path=/&name=uf-x', 'delete&path=', 'delete&path=/',
                'delete&path=/.'];
            foreach ($rootQueries as $query) {
                $I->assertSame(400, $fetch('/admin/fm?method=' . $query, $post)['status'], $query);
            }
            $I->assertDirectoryExists(codecept_root_dir('public/uf/images'));

            // соседняя с uf папка (uf_codecept), выход за корень и символическая ссылка на соседнюю папку
            $outsideQueries = ['getFileList&path=_codecept', 'getFileList&path=/../',
                'delete&path=/images/../../uf_codecept', 'getFileList&path=/images/codecept-fm-link',
                'delete&path=/images/codecept-fm-link', 'rename&path=/images/codecept-fm-link&name=x'];
            foreach ($outsideQueries as $query) {
                $options = str_starts_with($query, 'getFileList') ? [] : $post;
                $I->assertSame(400, $fetch('/admin/fm?method=' . $query, $options)['status'], $query);
            }
            $I->assertDirectoryExists($sibling);
            $I->assertTrue(is_link($link), 'ссылка не удалена и не переименована');
        } finally {
            if (is_link($link)) {
                unlink($link);
            }
            foreach ([$dir, codecept_root_dir('public/uf/images/codecept-fm-moved'), $sibling] as $path) {
                if (is_dir($path)) {
                    rmdir($path);
                }
            }
        }
    }

    public function tryToCheckSeveral(AcceptanceTester $I): void
    {
        // клик по файлу — выбор одного без галочек; галочка включает режим отметки, дальше клик по карточке отмечает
        // её, клик мимо не сбрасывает; «Отменить выбор» (вместо кнопок папки) — выход
        $dir = codecept_root_dir('public/uf/images/codecept-fm-check');
        mkdir($dir, 0777, true);
        foreach (['a', 'b', 'c'] as $name) {
            copy(codecept_data_dir('upload.png'), $dir . '/' . $name . '.png');
        }
        try {
            $I->amOnPage('/admin');
            $I->waitForJS('return typeof window.fileManager === "object"', 10);
            $I->executeJS('window.fileManager.open({select: true, multiple: true})');
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $folder = self::WINDOW . ' .fm-card[data-fm-path="/images/codecept-fm-check"]';
            $I->waitForElementVisible($folder, 10);
            $I->click($folder);
            $card = fn(string $name) => self::WINDOW
                . ' .fm-card[data-fm-path="/images/codecept-fm-check/' . $name . '.png"]';
            $I->waitForElementVisible($card('c'), 10);

            $checkIcon = fn(string $name) => $I->grabAttributeFrom($card($name) . ' .fm-card__check use', 'href');

            // выбранный кликом файл — без галочки (не ставится сама)
            $I->click($card('a'));
            $I->seeElement($card('a') . '[aria-selected="true"]');
            $I->dontSeeElement(self::WINDOW . ' .fm-files_multi');
            $I->assertStringEndsWith('#fm-checkbox-off', $checkIcon('a'));

            // галочка на b: режим отметки только с b — a без галочки в него не попадает
            $I->moveMouseOver($card('b'));
            $I->wait(0.4);
            $I->click($card('b') . ' .fm-card__check');
            $I->seeElement(self::WINDOW . ' .fm-files_multi');
            $I->seeElement($card('a') . '[aria-selected="false"]');
            $I->seeElement($card('b') . '[aria-selected="true"]');
            $I->assertStringEndsWith('#fm-checkbox-on', $checkIcon('b'));
            $I->waitForElementVisible(self::WINDOW . ' .fm-tools__cancel', 5);
            $I->click($card('a'));

            // дальше — клик по карточке; клик мимо карточек выбор не сбрасывает
            $I->click($card('c'));
            $I->seeElement($card('c') . '[aria-selected="true"]');
            $I->click(self::WINDOW . ' .fm-board');
            $I->see('Выбрано: 3', self::WINDOW . ' [data-fm-info-name]');

            $I->click(self::WINDOW . ' .fm-tools__cancel');
            $I->dontSeeElement(self::WINDOW . ' .fm-card[aria-selected="true"]');
            $I->dontSeeElement(self::WINDOW . ' .fm-files_multi');
            $I->executeJS('window.fileManager.close()');
        } finally {
            foreach (glob($dir . '/*') ?: [] as $path) {
                unlink($path);
            }
            if (is_dir($dir)) {
                rmdir($dir);
            }
        }
    }

    public function tryToMoveFilesAndFolders(AcceptanceTester $I): void
    {
        // «Переместить» в меню: окно выбора папки по шагам (вложенные папки, «назад», путь), перемещаемая папка
        // недоступна, «Переместить сюда» — не в ту же папку; несколько отмеченных — вместе
        $dir = codecept_root_dir('public/uf/images/codecept-fm-move');
        // права на запись и веб-серверу: тест создаёт папки от своего пользователя
        foreach (['dest/deeper', 'sub'] as $path) {
            mkdir($dir . '/' . $path, 0777, true);
        }
        foreach ([$dir, $dir . '/dest', $dir . '/dest/deeper', $dir . '/sub'] as $path) {
            chmod($path, 0777);
        }
        foreach (['a.png', 'b.png', 'sub/c.png'] as $name) {
            copy(codecept_data_dir('upload.png'), $dir . '/' . $name);
        }
        $moveDialog = '#sf-file-manager-move';
        $base = '/images/codecept-fm-move';
        try {
            $I->amOnPage('/admin');
            $I->waitForJS('return typeof window.fileManager === "object"', 10);
            $I->executeJS('window.fileManager.open({select: true, multiple: true})');
            $I->waitForElementVisible(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]', 10);
            $I->click(self::WINDOW . ' .fm-tree-row[data-fm-path="/images"]');
            $folder = self::WINDOW . ' .fm-card[data-fm-path="' . $base . '"]';
            $I->waitForElementVisible($folder, 10);
            $I->click($folder);
            $card = fn(string $name) => self::WINDOW . ' .fm-card[data-fm-path="' . $base . '/' . $name . '"]';
            $I->waitForElementVisible($card('b.png'), 10);

            // отмечены a.png и папка sub
            $I->moveMouseOver($card('a.png'));
            $I->wait(0.4);
            $I->click($card('a.png') . ' .fm-card__check');
            $I->click($card('sub'));
            $I->see('Выбрано: 2', self::WINDOW . ' [data-fm-info-name]');

            $I->clickWithRightButton($card('a.png'));
            $I->click(self::WINDOW . ' [data-fm-menu="file"] [data-fm-action="move"]');
            $I->waitForElementVisible($moveDialog, 5);
            $I->see('Выбрано: 2', $moveDialog . ' [data-fm-text]');
            $I->waitForElementVisible($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/dest"]', 10);
            $I->seeElement($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/sub"][disabled]');
            $I->seeElement($moveDialog . ' [data-fm-action="move-here"][disabled]');
            $I->see('сейчас здесь', $moveDialog);

            // в dest, «назад» — обратно, снова в dest
            $I->click($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/dest"]');
            $I->waitForElementVisible($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/dest/deeper"]', 10);
            $I->click($moveDialog . ' [data-fm-move-back]');
            $I->waitForElementVisible($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/dest"]', 10);
            $I->click($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/dest"]');
            $I->waitForElementVisible($moveDialog . ' .fm-move__row[data-fm-move-to="' . $base . '/dest/deeper"]', 10);
            $I->waitForElement($moveDialog . ' [data-fm-action="move-here"]:not([disabled])', 10);
            $I->click($moveDialog . ' [data-fm-action="move-here"]');
            $I->waitForElementNotVisible($moveDialog, 10);
            $I->waitForElementNotVisible($card('a.png'), 10);
            $I->assertFileExists($dir . '/dest/a.png');
            $I->assertFileExists($dir . '/dest/sub/c.png');
            $I->assertFileExists($dir . '/b.png');
            $I->dontSeeElement(self::WINDOW . ' .fm-files_multi');

            // API: папка — не в саму себя и не во вложенную, имя занято, корень, без токена
            $token = $I->executeJS('return document.querySelector(\'meta[name="csrf-token"]\').content');
            $move = fn(string $path, array $items, string $csrf = '') => $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1]; const body = new FormData();
                arguments[1].forEach((item) => body.append("items[]", item));
                fetch("/admin/fm?method=move&path=" + encodeURIComponent(arguments[0]),
                    {method: "POST", body, headers: {"X-CSRF-Token": arguments[2]}}).then((r) => done(r.status));',
                [$path, $items, $csrf ?: $token]
            );
            copy(codecept_data_dir('upload.png'), $dir . '/dest/b.png');
            $I->assertSame(422, $move($base . '/dest/sub', [$base . '/dest']));
            $I->assertSame(422, $move($base . '/dest', [$base . '/dest']));
            $I->assertSame(422, $move($base . '/dest', [$base . '/dest/a.png']));
            $I->assertSame(409, $move($base . '/dest', [$base . '/b.png']));
            $I->assertSame(400, $move($base, ['/']));
            $I->assertSame(400, $move($base, [$base . '/../codecept-fm-move/b.png']));
            $I->assertSame(400, $move($base, []));
            $I->assertSame(403, $move($base, [$base . '/dest/a.png'], 'x'));
            $I->assertFileExists($dir . '/b.png');
            $I->assertFileExists($dir . '/dest/a.png');
            $I->executeJS('window.fileManager.close()');
        } finally {
            $rm = function (string $path) use (&$rm): void {
                foreach (glob($path . '/*') ?: [] as $item) {
                    is_dir($item) ? $rm($item) : unlink($item);
                }
                if (is_dir($path)) {
                    rmdir($path);
                }
            };
            $rm($dir);
        }
    }

    public function tryToWarnAboutUsage(AcceptanceTester $I): void
    {
        // файл стоит в записи: API usage находит таблицу, подтверждение удаления предупреждает
        $file = codecept_root_dir('public/uf/images/codecept-usage.png');
        copy(codecept_data_dir('upload.png'), $file);
        $I->runSql('CREATE TABLE codecept_usage (id INT PRIMARY KEY, body TEXT)');
        $I->runSql(
            'INSERT INTO codecept_usage VALUES (1, ?)',
            ['<p><img src="/uf/images/codecept-usage.png" alt=""></p>']
        );
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
            $I->assertTrue(
                in_array(['table' => 'codecept_usage', 'count' => 1], $free),
                'ссылка на файл папки — ссылка на папку'
            );

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
        // загрузка: имя латиницей, занятое не перезаписывается; PNG сжимается в WebP (до 2560 px) только с согласия
        $dir = codecept_root_dir('public/uf/images/codecept-fm-upload');
        mkdir($dir, 0777, true);
        // тест и веб-сервер — разные пользователи: папка доступна на запись сайту (mkdir урезает права по umask)
        chmod($dir, 0777);
        try {
            $I->amOnPage('/admin');
            // сжатие большой картинки на сервере — дольше обычного ожидания скрипта
            $I->executeInSelenium(fn($driver) => $driver->manage()->timeouts()->setScriptTimeout(60));
            $upload = fn(string $compress): array => $I->executeAsyncJS(
                'const done = arguments[arguments.length - 1];
                const compress = arguments[0];
                const canvas = document.createElement("canvas");
                canvas.width = 3000;
                canvas.height = 1000;
                canvas.getContext("2d").fillRect(0, 0, 10, 10);
                canvas.toBlob((blob) => {
                    const body = new FormData();
                    body.append("file[]", new File([blob], "Фото Отпуска.PNG", {type: "image/png"}));
                    body.append("compress", compress);
                    fetch("/admin/fm?method=upload&path=/images/codecept-fm-upload", {method: "POST", body,
                        headers: {"X-CSRF-Token": document.querySelector(\'meta[name="csrf-token"]\').content}})
                        .then(r => r.json()).then(json => done(json.items));
                }, "image/png");',
                [$compress]
            );

            // с согласия — WebP до 2560 px
            $I->assertSame(['/images/codecept-fm-upload/foto-otpuska.webp'], $upload('1'));
            $I->assertSame(['/images/codecept-fm-upload/foto-otpuska-2.webp'], $upload('1'));
            $size = getimagesize($dir . '/foto-otpuska.webp');
            $I->assertSame([2560, 853, IMAGETYPE_WEBP], [$size[0], $size[1], $size[2]]);
            // без согласия — как есть
            $I->assertSame(['/images/codecept-fm-upload/foto-otpuska.png'], $upload('0'));
            $size = getimagesize($dir . '/foto-otpuska.png');
            $I->assertSame([3000, 1000, IMAGETYPE_PNG], [$size[0], $size[1], $size[2]]);
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
            $I->assertFalse($I->executeJS(
                'return document.querySelector(arguments[0]).hasAttribute("inert")',
                [$modal]
            ));
        } finally {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
