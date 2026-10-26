<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Действия раздела-таблицы (Table\Table), идущие через модель и построитель запросов таблицы:
 * копирование и порядок (на меню сайта: его записи не нужны другим тестам), сортировка и страницы, фильтры списка,
 * путь страницы
 */
class TableActionsCest
{
    private const SECTION = '/admin/site/menu';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsDev();
    }

    public function tryToCopyAndReorder(AcceptanceTester $I): void
    {
        $I->amOnPage(self::SECTION);
        $source = $I->grabRowsFromSql('SELECT menu_id, name, npp FROM menu ORDER BY menu_id LIMIT 1')[0];
        try {
            $this->fetch($I, self::SECTION . '?action=copy&ids=' . $source['menu_id']);
            $copies = $I->grabRowsFromSql('SELECT menu_id, name FROM menu WHERE menu_id > ?', [$source['menu_id']]);
            $I->assertCount(1, $copies);
            $I->assertSame('Копия ' . $source['name'], $copies[0]['name']);
            $id = $copies[0]['menu_id'];

            $this->fetch($I, self::SECTION . "?action=change_npp&field=npp&menu_id=$id&val=10");
            $I->assertSame([['npp' => 10]], $I->grabRowsFromSql('SELECT npp FROM menu WHERE menu_id = ?', [$id]));
            $this->fetch($I, self::SECTION . "?action=change_npp&field=npp&menu_id=$id&up=1");
            $I->assertSame([['npp' => 9]], $I->grabRowsFromSql('SELECT npp FROM menu WHERE menu_id = ?', [$id]));
        } finally {
            $I->runSql('DELETE FROM menu WHERE menu_id > ?', [$source['menu_id']]);
        }
        $I->dontSeeLogProblems();
    }

    public function tryToFilterList(AcceptanceTester $I): void
    {
        // роль — внешний ключ: её название приходит через join
        $I->amOnPage('/admin/admin/role_privilege?filter[privilege]=');
        $I->dontSeeErrors();
        $I->see('Разработчик', '.table__body');
        $I->see('Администратор', '.table__body');

        // значения фильтров привязываются: кавычка не ломает запрос
        $I->amOnPage('/admin/admin/role_privilege?filter[privilege]=manager');
        $I->see('Администратор', '.table__body');
        $I->dontSee('Разработчик', '.table__body');
        $I->amOnPage("/admin/admin/role_privilege?filter[privilege]=a'b");
        $I->dontSeeErrors();
        $I->dontSee('Администратор', '.table__body');

        $I->amOnPage('/admin/admin/role_privilege?filter[privilege]=');
        $I->see('Разработчик', '.table__body');

        // строковое поле фильтруется по подстроке
        $I->amOnPage('/admin/admin/user?filter[login]=dmi');
        $I->dontSeeErrors();
        $I->see('admin', '.table__body');
        $I->dontSee('dev@simflex', '.table__body');
        $I->amOnPage("/admin/admin/user?filter[login]=a'b");
        $I->dontSeeErrors();
        $I->amOnPage('/admin/admin/user?filter[login]=');
        $I->see('dev@simflex', '.table__body');
        $I->dontSeeLogProblems();
    }

    public function tryToSavePathOfChildPage(AcceptanceTester $I): void
    {
        $I->runSql(
            "INSERT INTO content SET pid = 1, active = 0, title = 'Codecept child', alias = 'codecept-child', path = '',"
            . " short = '', text = '', params = '', file = '', photo = '', template = '', npp = 0"
        );
        $id = $I->grabRowsFromSql("SELECT content_id FROM content WHERE alias = 'codecept-child'")[0]['content_id'];
        try {
            $I->amOnPage("/admin/content/?action=form&content_id=$id");
            $I->submitAndWaitForReload('.content__btns button[name="submit_apply"]', '.sidebar');
            $I->dontSeeErrors();
            $I->assertSame(
                [['path' => '/codecept-child/']],
                $I->grabRowsFromSql('SELECT path FROM content WHERE content_id = ?', [$id])
            );
        } finally {
            $I->runSql("DELETE FROM content WHERE alias = 'codecept-child'");
        }
        $I->dontSeeLogProblems();
    }

    public function tryToSortAndPage(AcceptanceTester $I): void
    {
        // 25 записей: 20 на первой странице, 5 на второй
        for ($i = 1; $i <= 25; $i++) {
            $I->runSql(
                "INSERT INTO seo SET seo_pid = 0, link = ?, title = ?, description = '', keywords = '', metatags = ''",
                ['/codecept-page', sprintf('Codecept %02d', $i)]
            );
        }
        try {
            $I->amOnPage('/admin/site/seo?filter[link]=codecept-page');
            $I->dontSeeErrors();
            $I->assertSame(20, $this->rowCount($I));
            $I->amOnPage('/admin/site/seo?p=1');
            $I->assertSame(5, $this->rowCount($I));

            // клик по заголовку сортирует по колонке, второй меняет порядок на обратный
            $I->amOnPage('/admin/site/seo?o=title&p=0');
            $I->seeElement('.table-data-title .form-control__sort-up--active');
            $I->assertSame('Codecept 01', $this->firstTitle($I));
            $I->amOnPage('/admin/site/seo?o=title&p=0');
            $I->seeElement('.table-data-title .form-control__sort-down--active');
            $I->assertSame('Codecept 25', $this->firstTitle($I));

            // неизвестная колонка игнорируется
            $I->amOnPage('/admin/site/seo?o=no_such_column');
            $I->dontSeeErrors();
            $I->amOnPage('/admin/site/seo?filter[link]=');
        } finally {
            $I->runSql("DELETE FROM seo WHERE link = '/codecept-page'");
        }
        $I->dontSeeLogProblems();
    }

    private function rowCount(AcceptanceTester $I): int
    {
        // в DOM: список прокручивается, записи за пределами экрана для WebDriver не "видимы"
        return (int)$I->executeJS("return document.querySelectorAll('.table__body-row').length");
    }

    private function firstTitle(AcceptanceTester $I): string
    {
        return trim($I->grabTextFrom('.table__body-row:first-child td[data-field="title"]'));
    }

    private function fetch(AcceptanceTester $I, string $url): void
    {
        $status = $I->executeAsyncJS(
            'const done = arguments[arguments.length - 1];
            fetch(arguments[0], {credentials: "same-origin"}).then(r => done(r.status)).catch(() => done(0));',
            [$url]
        );
        $I->assertSame(200, $status, $url);
    }
}
