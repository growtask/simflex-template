<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * "Редактор страниц": список, форма с полями шаблона, сохранение, ajax-действия списка
 */
class ContentCest
{
    private const FORM = '/admin/content/?action=form&content_id=1';
    private const APPLY = '.content__btns button[name="submit_apply"]';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToSeeList(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/content');
        $I->dontSeeErrors();
        $I->seeInTitle('Редактор страниц');
        $I->seeElement('table');
        $I->see('Главная');
        $I->seeElementInDOM('a[href*="action=form&content_id=1"]');
        $I->dontSeeLogProblems();
    }

    public function tryToSeeForm(AcceptanceTester $I): void
    {
        $I->amOnPage(self::FORM);
        $I->dontSeeErrors();
        $I->seeInTitle('Редактор страниц');
        $I->seeInField('title', 'Главная');

        // поля шаблона страницы, объявленные в коде (provider/templates.php), в своих блоках
        $I->seeInField('input[name="template"]', 'index');
        $I->see('Главная', $I->dropdown('template'));
        $I->seeElement('input[name="meta_title"]');
        $I->seeElement('input[name="seo_title_1"]');
        $I->see('SEO', '.data2__title');
        $I->see('Мета', '.data3__title');

        $I->dontSeeLogProblems();
    }

    public function tryToSaveForm(AcceptanceTester $I): void
    {
        $value = 'Codeception test ' . time();
        try {
            $I->amOnPage(self::FORM);
            $I->fillField('meta_title', $value);
            $I->submitAndWaitForReload(self::APPLY, 'input[name="meta_title"]');
            $I->dontSeeErrors();
            $I->seeInCurrentUrl('action=form');

            // перезагружаем с нуля, чтобы убедиться, что значение сохранено, а не просто осталось в DOM
            $I->amOnPage(self::FORM);
            $I->seeInField('meta_title', $value);
            // хранится в content.params по имени поля
            $params = unserialize($I->grabRowsFromSql('SELECT params FROM content WHERE content_id = 1')[0]['params']);
            $I->assertSame($value, $params['meta_title']);
        } finally {
            $I->amOnPage(self::FORM);
            $I->fillField('meta_title', '');
            $I->submitAndWaitForReload(self::APPLY, 'input[name="meta_title"]');
            $I->seeInField('meta_title', '');
        }

        $I->dontSeeLogProblems();
    }

    public function tryToSaveGroupWithQuoteInValue(AcceptanceTester $I): void
    {
        // кавычка в значении раньше ломала групповой UPDATE, собранный подстановкой в строку
        $value = "Group O'Test " . uniqid();
        try {
            $I->amOnPage('/admin/content/?action=form&ids=1,1');
            $I->seeInField('group_ids', '1,1');
            $I->executeJS('document.querySelector(\'input[name="set[title]"]\').checked = true;');
            $I->fillField('title', $value);
            $I->submitAndWaitForReload(self::APPLY, '.sidebar');
            $I->dontSeeErrors();
            $I->see('успешно сохранены');

            $I->amOnPage(self::FORM);
            $I->seeInField('title', $value);
        } finally {
            $I->runSql('UPDATE content SET title = ? WHERE content_id = 1', ['Главная']);
        }

        $I->dontSeeLogProblems();
    }

    public function tryToCallListActions(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/content/');

        $validate = $this->fetch($I, '/admin/content/?ajax=1&action=validate', 'POST');
        $I->assertSame(200, $validate['status']);
        $I->assertArrayHasKey('valid', json_decode($validate['body'], true) ?? [], $validate['body']);

        try {
            $toggle = '/admin/content/?ajax=1&action=boolChange&field=active&pk=1';
            $I->assertSame('0', $this->fetch($I, $toggle)['body']);
            $I->assertSame('1', $this->fetch($I, $toggle)['body']);
        } finally {
            $I->runSql('UPDATE content SET active = 1 WHERE content_id = 1');
        }

        $detail = $this->fetch($I, '/admin/content/?ajax=1&action=showDetail&id=1')['body'];
        $I->assertStringContainsString('modal-info__table', $detail);
        $I->assertStringContainsString('Главная', $detail);

        $I->dontSeeLogProblems();
    }

    public function tryToUploadImage(AcceptanceTester $I): void
    {
        $photo = 'input[type="hidden"][name="photo"]';
        $before = $I->grabRowsFromSql('SELECT photo FROM content WHERE content_id = 1')[0]['photo'];
        $name = '';
        try {
            $I->amOnPage(self::FORM);
            // файл загружается через ajax (?action=upload_img), его имя попадает в скрытое поле
            $I->attachFile("//input[@name='photo']/ancestor::div[contains(@class, 'form-control__file')][1]//input[@type='file']", 'upload.png');
            $I->waitForJS("return document.querySelector('$photo').value !== ''", 10);
            // в поле — url загруженного исходника, в записи хранится имя файла
            $name = basename($I->grabValueFrom($photo));
            $I->submitAndWaitForReload(self::APPLY, 'input[name="meta_title"]');
            $I->dontSeeErrors();

            $I->assertSame([['photo' => $name]], $I->grabRowsFromSql('SELECT photo FROM content WHERE content_id = 1'));
            $I->assertNotEmpty($this->uploaded($name), "$name is not saved");
            $I->seeElement("img.form-control__file-img[src*='$name']");
        } finally {
            $I->runSql('UPDATE content SET photo = ? WHERE content_id = 1', [$before]);
            foreach ($name ? $this->uploaded($name) : [] as $file) {
                unlink($file);
            }
        }
        $I->dontSeeLogProblems();
    }

    /**
     * @return string[] файлы загруженного изображения: исходник и размеры
     */
    private function uploaded(string $name): array
    {
        $files = [];
        $dir = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(codecept_root_dir() . 'public/uf/images/content', \FilesystemIterator::SKIP_DOTS));
        foreach ($dir as $file) {
            if ($file->getFilename() === $name) {
                $files[] = $file->getPathname();
            }
        }
        return $files;
    }

    /**
     * @return array{status: int, body: string}
     */
    private function fetch(AcceptanceTester $I, string $url, string $method = 'GET'): array
    {
        return $I->executeAsyncJS(
            'const done = arguments[arguments.length - 1];
            fetch(arguments[0], {
                method: arguments[1],
                credentials: "same-origin",
                headers: {"X-Requested-With": "XMLHttpRequest"},
            })
                .then(r => r.text().then(body => done({status: r.status, body})))
                .catch(e => done({status: 0, body: String(e)}));',
            [$url, $method]
        );
    }
}
