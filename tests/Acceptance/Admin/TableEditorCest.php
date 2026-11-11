<?php

namespace Tests\Acceptance\Admin;

use Tests\Support\AcceptanceTester;

/**
 * Таблица строк в форме (theme/js/table-editor.js): FieldTable — строки в поле формы (слайды и преимущества главной
 * страницы, provider/templates.php), FieldVirtualTable — строки другой таблицы, сохраняются сразу (фото отзыва)
 */
class TableEditorCest
{
    private const CONTENT_FORM = '/admin/content/page/?action=form&content_id=1';
    private const APPLY = '.edit-bar button[name="submit_apply"]';
    private const BENEFITS = '[data-sf="table-editor"]:has([data-sf-table-value][name="benefits"])';
    private const PHOTOS = '[data-sf="table-editor"][data-sf-table-mode="virtual"]';

    public function _before(AcceptanceTester $I): void
    {
        $I->loginAsAdmin();
    }

    public function tryToEditTableField(AcceptanceTester $I): void
    {
        $saved = $I->grabRowsFromSql('SELECT params FROM content WHERE content_id = 1')[0]['params'];
        try {
            $I->amOnPage(self::CONTENT_FORM);
            $I->dontSeeErrors();
            $rows = $this->countRows($I, self::BENEFITS);

            // новая строка: окно с полем на каждую колонку
            $this->press($I, self::BENEFITS . ' [data-sf-table-add]');
            $modal = $this->modal($I, self::BENEFITS);
            $I->waitForElementVisible($modal . ' [data-sf-column="title"]', 5);
            $I->fillField($modal . ' [data-sf-column="title"]', 'Codecept benefit');
            $I->fillField($modal . ' [data-sf-column="text"]', 'Codecept text');
            $I->click($modal . ' [data-sf-table-save]');
            $I->waitForElementNotVisible($modal, 5);
            $I->assertSame($rows + 1, $this->countRows($I, self::BENEFITS));
            $I->see('Codecept benefit', self::BENEFITS);

            // изменение строки: окно заполнено значениями строки
            $this->press($I, self::BENEFITS . ' [data-sf-table-row="' . $rows . '"] [data-sf-table-edit]');
            $I->waitForElementVisible($modal . ' [data-sf-column="title"]', 5);
            $I->seeInField($modal . ' [data-sf-column="title"]', 'Codecept benefit');
            $I->fillField($modal . ' [data-sf-column="title"]', 'Codecept benefit 2');
            $I->click($modal . ' [data-sf-table-save]');
            $I->waitForElementNotVisible($modal, 5);

            // строки сохраняются вместе с формой
            $I->submitAndWaitForReload(self::APPLY, self::BENEFITS);
            $I->dontSeeErrors();
            $benefits = $this->grabBenefits($I);
            $I->assertCount($rows + 1, $benefits);
            $I->assertSame('Codecept benefit 2', $benefits[$rows]['title']);
            $I->assertSame('Codecept text', $benefits[$rows]['text']);

            // удаление — после подтверждения
            $this->press($I, self::BENEFITS . ' [data-sf-table-row="' . $rows . '"] [data-sf-table-delete]');
            $I->waitForElementVisible('#sf-modal-confirm [data-sf-confirm="yes"]', 5);
            $I->click('#sf-modal-confirm [data-sf-confirm="yes"]');
            $I->waitForElementNotVisible('#sf-modal-confirm', 5);
            $I->assertSame($rows, $this->countRows($I, self::BENEFITS));
            $I->submitAndWaitForReload(self::APPLY, self::BENEFITS);
            $I->assertCount($rows, $this->grabBenefits($I));
        } finally {
            $I->runSql('UPDATE content SET params = ? WHERE content_id = 1', [$saved]);
        }
        $I->dontSeeLogProblems();
    }

    public function tryToEditVirtualTable(AcceptanceTester $I): void
    {
        $I->runSql("INSERT INTO review SET product_id = 0, name = 'codecept', comment = 'Codecept review'");
        $reviewId = (int)$I->grabRowsFromSql("SELECT review_id FROM review WHERE name = 'codecept'")[0]['review_id'];
        $I->runSql('INSERT INTO review_file SET review_id = ?, path = ?', [$reviewId, '/codecept-1.webp']);
        try {
            $I->amOnPage('/admin/catalog/review/?action=form&review_id=' . $reviewId);
            $I->dontSeeErrors();
            $I->assertSame(1, $this->countRows($I, self::PHOTOS));

            // новая строка сохраняется сразу действием vtAdd (картинка — адрес в поле файла)
            $this->press($I, self::PHOTOS . ' [data-sf-table-add]');
            $modal = $this->modal($I, self::PHOTOS);
            $I->waitForElementVisible($modal . ' [data-sf-table-upload]', 5);
            $I->executeJS(
                'document.querySelector(arguments[0]).value = "/codecept-2.webp"',
                [$modal . ' [data-sf-column="img"]']
            );
            $I->click($modal . ' [data-sf-table-save]');
            $I->waitForElementNotVisible($modal, 5);
            $I->waitForElement(self::PHOTOS . ' img[src="/codecept-2.webp"]', 5);
            $I->assertSame(['/codecept-1.webp', '/codecept-2.webp'], $this->grabPaths($I, $reviewId));

            // изменение — vtEdit, окно заполнено значением строки
            $this->press($I, self::PHOTOS . ' .tr:first-child [data-sf-table-edit]');
            $I->waitForElementVisible($modal . ' [data-sf-table-upload]', 5);
            $I->assertSame('/codecept-1.webp', $I->executeJS(
                'return document.querySelector(arguments[0]).value',
                [$modal . ' [data-sf-column="img"]']
            ));
            $I->executeJS(
                'document.querySelector(arguments[0]).value = "/codecept-3.webp"',
                [$modal . ' [data-sf-column="img"]']
            );
            $I->click($modal . ' [data-sf-table-save]');
            $I->waitForElementNotVisible($modal, 5);
            $I->waitForElement(self::PHOTOS . ' img[src="/codecept-3.webp"]', 5);

            // удаление — vtDelete после подтверждения
            $this->press($I, self::PHOTOS . ' .tr:first-child [data-sf-table-delete]');
            $I->waitForElementVisible('#sf-modal-confirm [data-sf-confirm="yes"]', 5);
            $I->click('#sf-modal-confirm [data-sf-confirm="yes"]');
            $I->waitForElementNotVisible(self::PHOTOS . ' img[src="/codecept-3.webp"]', 5);
            $I->assertSame(['/codecept-2.webp'], $this->grabPaths($I, $reviewId));
        } finally {
            $I->runSql('DELETE FROM review_file WHERE review_id = ?', [$reviewId]);
            $I->runSql('DELETE FROM review WHERE review_id = ?', [$reviewId]);
        }
    }

    public function tryToSeeVirtualTableNoticeOnNewRecord(AcceptanceTester $I): void
    {
        // строки ссылаются на запись: у новой записи — уведомление вместо таблицы
        $I->amOnPage('/admin/catalog/review/?action=form');
        $I->dontSeeErrors();
        $I->seeElement('.edit-table-notice');
        $I->dontSeeElement(self::PHOTOS);
    }

    /** Кнопка таблицы: нажатие скриптом — таблица ниже первого экрана, её закрывает панель сохранения */
    private function press(AcceptanceTester $I, string $selector): void
    {
        $I->seeElementInDOM($selector);
        $I->executeJS('document.querySelector(arguments[0]).click()', [$selector]);
    }

    private function countRows(AcceptanceTester $I, string $table): int
    {
        return (int)$I->executeJS(
            'return document.querySelectorAll(arguments[0]).length',
            [$table . ' [data-sf-table-row]']
        );
    }

    /** Окно строки таблицы: при открытии переносится в body, ищется по id из корня таблицы */
    private function modal(AcceptanceTester $I, string $table): string
    {
        $id = $I->executeJS('return document.querySelector(arguments[0]).dataset.sfTableModal', [$table]);
        return '#' . $id;
    }

    /** @return list<array<string, string>> */
    private function grabBenefits(AcceptanceTester $I): array
    {
        $params = unserialize($I->grabRowsFromSql('SELECT params FROM content WHERE content_id = 1')[0]['params']);
        return json_decode($params['benefits'], true)['v'];
    }

    /** @return list<string> */
    private function grabPaths(AcceptanceTester $I, int $reviewId): array
    {
        $rows = $I->grabRowsFromSql('SELECT path FROM review_file WHERE review_id = ? ORDER BY rf_id', [$reviewId]);
        return array_column($rows, 'path');
    }
}
