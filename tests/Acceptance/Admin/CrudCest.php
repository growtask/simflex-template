<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Создание, чтение, изменение и удаление через интерфейс раздела-таблицы: «Настройки сайта → SEO»
 */
class CrudCest
{
    private const SECTION = '/admin/site/seo';
    private const LINK = '/codecept-crud';
    private const SAVE = '.content__btns button[name="submit_save"]';

    public function _before(AcceptanceTester $I): void
    {
        $I->runSql('DELETE FROM seo WHERE link = ?', [self::LINK]);
        $I->loginAsDev();
    }

    public function _after(AcceptanceTester $I): void
    {
        $I->runSql('DELETE FROM seo WHERE link = ?', [self::LINK]);
    }

    public function tryToCreateReadUpdateDelete(AcceptanceTester $I): void
    {
        // создание: «Добавить» в списке, форма, «Сохранить и выйти» обратно в список
        $I->amOnPage(self::SECTION);
        $I->dontSeeErrors();
        $I->click('a[href="?action=form"]');
        $I->waitForElement('input[name="link"]', 10);
        $I->fillField('link', self::LINK);
        $I->fillField('title', 'Codecept title');
        $I->fillField('description', 'Codecept description');
        $I->submitAndWaitForReload(self::SAVE, '.table__body');
        $I->dontSeeErrors();
        $rows = $I->grabRowsFromSql('SELECT seo_id, title, description FROM seo WHERE link = ?', [self::LINK]);
        $I->assertCount(1, $rows);
        $I->assertSame('Codecept title', $rows[0]['title']);
        $I->assertSame('Codecept description', $rows[0]['description']);
        $id = $rows[0]['seo_id'];

        // чтение: запись есть в списке
        $I->see(self::LINK, '.table__body');
        $I->see('Codecept title', '.table__body');
        // «Удалить» записи в мобильном меню тоже удаляет, а не открывает форму
        $I->seeElementInDOM(
            "//tr[td[@data-pk='$id']]//div[contains(@class, 'table__body-item-settings-btns')]"
            . "//button[contains(@onclick, 'onRowDeleteRequest($id,')]"
        );

        // изменение: «Редактировать» записи, в форме есть значения
        $I->click($this->rowButton($id, 'tooltip-row1-edit', 'a'));
        $I->waitForElement('input[name="link"]', 10);
        $I->seeInField('link', self::LINK);
        $I->seeInField('title', 'Codecept title');
        $I->fillField('title', 'Codecept title 2');
        $I->submitAndWaitForReload(self::SAVE, '.table__body');
        $I->dontSeeErrors();
        $I->assertSame(
            [['title' => 'Codecept title 2', 'description' => 'Codecept description']],
            $I->grabRowsFromSql('SELECT title, description FROM seo WHERE seo_id = ?', [$id])
        );
        $I->see('Codecept title 2', '.table__body');

        // удаление: «Удалить» записи с подтверждением в диалоге
        $I->click($this->rowButton($id, 'tooltip-row1-delete', 'button'));
        $I->waitForElementVisible('.modal-delete__btns a', 10);
        $I->click('.modal-delete__btns a');
        $I->waitForText('Запись удалена', 10);
        $I->assertSame([], $I->grabRowsFromSql('SELECT seo_id FROM seo WHERE seo_id = ?', [$id]));
        $I->dontSee(self::LINK, '.table__body');
        $I->dontSeeLogProblems();
    }

    public function tryToDeleteSelectedRows(AcceptanceTester $I): void
    {
        foreach (['Codecept 1', 'Codecept 2'] as $title) {
            $I->runSql(
                "INSERT INTO seo SET seo_pid = 0, link = ?, title = ?, description = '', keywords = '', metatags = ''",
                [self::LINK, $title]
            );
        }
        $ids = array_column($I->grabRowsFromSql('SELECT seo_id FROM seo WHERE link = ?', [self::LINK]), 'seo_id');

        $I->amOnPage(self::SECTION);
        foreach ($ids as $id) {
            $I->checkOption("//tr[td[@data-pk='$id']]//input[@type='checkbox']");
        }
        $I->click('#btn-control-delete');
        $I->waitForText('Запись удалена', 10);
        $I->assertSame([], $I->grabRowsFromSql('SELECT seo_id FROM seo WHERE link = ?', [self::LINK]));
        $I->dontSeeLogProblems();
    }

    private function rowButton(string|int $id, string $tooltip, string $tag): string
    {
        return "//tr[td[@data-pk='$id']]//div[contains(@class, '$tooltip')]//$tag";
    }
}
