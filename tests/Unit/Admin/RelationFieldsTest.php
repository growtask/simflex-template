<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldMultiKey;
use Simflex\Admin\Fields\FieldRelation;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\MultiKeyField;
use Simflex\Admin\Table\Repository;
use Simflex\Admin\Table\Table;
use Tests\Support\MockDb;

/**
 * Поля связей FieldMultiKey и FieldRelation: значения передаются параметрами запросов, у новой записи (без id)
 * поля ничего не пишут
 */
class RelationFieldsTest extends Unit
{
    use MockDb;

    private array $postBackup = [];
    private array $requestBackup = [];

    protected function _before(): void
    {
        $this->postBackup = $_POST;
        $this->requestBackup = $_REQUEST;
        $_POST = $_REQUEST = [];
        Table::$currentQuery = null;
    }

    protected function _after(): void
    {
        $_POST = $this->postBackup;
        $_REQUEST = $this->requestBackup;
    }

    public function testMultiKeySavesNothingWithoutRecordId(): void
    {
        $this->mockRows([], $queries);
        $_POST['p2p_add'] = '3,4';

        $this->assertSame('', $this->makeMultiKey()->getPOST());
        $this->assertSame([], $queries);
    }

    public function testMultiKeySavesRelationsWithParams(): void
    {
        $this->mockRows([], $queries);
        $_REQUEST['product_id'] = '5';
        $_POST['p2p_add'] = "3,3,0,4,x' OR 1";

        $this->makeMultiKey()->getPOST();
        // id записи — в колонке relation_key, выбранные — в key; повторы и мусор отброшены
        $this->assertSame([
            ['DELETE FROM `catalog_p2p_add` WHERE `link_id` = ?', [5]],
            ['INSERT INTO `catalog_p2p_add` (`link_id`, `product_id`) VALUES (?, ?)', [5, 3]],
            ['INSERT INTO `catalog_p2p_add` (`link_id`, `product_id`) VALUES (?, ?)', [5, 4]],
        ], $queries);
    }

    public function testMultiKeyRelationKeyDefaultsToPrimaryKey(): void
    {
        $this->assertSame('product_id', $this->makeMultiKey(relationKey: '')->getRelationKey());
        $this->assertSame('link_id', $this->makeMultiKey()->getRelationKey());
    }

    public function testMultiKeyValuesWithParentAndSearch(): void
    {
        $this->mockRows([
            ['id' => 2, 'name' => 'Футболки', 'parent' => 'Одежда'],
            ['id' => 1, 'name' => 'Одежда', 'parent' => null],
        ], $queries);
        $field = $this->makeMultiKey(values: 'catalog_category', key: 'category_id', parentKey: 'pid');

        $this->assertSame([2 => 'Футболки (Одежда)', 1 => 'Одежда'], $field->getValues(search: "x' OR 1", limit: 50));
        [$sql, $binds] = $queries[0];
        $this->assertStringContainsString('LEFT JOIN `catalog_category` `p` ON `t`.`pid` = `p`.`category_id`', $sql);
        $this->assertStringContainsString('t.`name` LIKE :search', $sql);
        $this->assertStringNotContainsString("x'", $sql);
        $this->assertSame(['search' => "%x' OR 1%"], $binds);
    }

    public function testMultiKeyInputOfNewRecord(): void
    {
        $this->mockRows([['id' => 3, 'name' => 'Товар']], $queries);

        $html = $this->makeMultiKey()->input('');
        // у новой записи связей нет: только запрос выбираемых записей
        $this->assertCount(1, $queries);
        $this->assertStringContainsString('data-value="3"', $html);
        $this->assertStringContainsString('value=""', $html);
    }

    public function testMultiKeyListLabels(): void
    {
        $this->mockRows([
            // связи записей списка, затем подписи выбранных записей
            ['record_id' => 5, 'value_id' => 3],
            ['record_id' => 5, 'value_id' => 4],
            false,
            ['id' => 3, 'name' => 'Товар 3', 'parent' => null],
            ['id' => 4, 'name' => 'Товар 4', 'parent' => null],
        ], $queries);

        $this->assertStringContainsString('Товар 3, Товар 4', $this->makeMultiKey()->show(['product_id' => 5]));
        $this->assertStringContainsString('SELECT r.`link_id` AS record_id', $queries[0][0]);
    }

    public function testRelationSavesNothingWithoutRecordId(): void
    {
        $this->mockRows([], $queries);
        $_POST['p2p'] = '6,7';

        $this->assertSame('', $this->makeRelation()->getPOST());
        $this->assertSame([], $queries);
    }

    public function testRelationSavesPairsWithParams(): void
    {
        // запись 5 была связана с 8
        $this->mockRows([['id' => 8, 'name' => 'B']], $queries);
        $_REQUEST['product_id'] = '5';
        $_POST['p2p'] = '6,7,5';

        $this->makeRelation()->getPOST();
        [$select, $deleteOld, $deleteNew, $insert] = $queries;
        $this->assertSame(['relation_id1' => 5, 'relation_id2' => 5], $select[1]);
        // прежняя группа (5, 8) и новая (5, 6, 7) — без пар между собой, затем новая связана попарно
        $this->assertStringStartsWith('DELETE FROM `catalog_p2p` WHERE `left_id` IN (', $deleteOld[0]);
        $this->assertSame([5, 8, 5, 8], array_values($deleteOld[1]));
        $this->assertSame([5, 6, 7, 5, 6, 7], array_values($deleteNew[1]));
        $this->assertSame(
            ['INSERT INTO `catalog_p2p` (`left_id`, `right_id`) VALUES (?, ?), (?, ?), (?, ?)', [5, 6, 5, 7, 6, 7]],
            $insert,
        );
    }

    private function makeMultiKey(
        string $values = 'catalog_product',
        string $key = 'product_id',
        string $relationKey = 'link_id',
        string $parentKey = '',
    ): FieldMultiKey {
        $field = new FieldMultiKey(Repository::toFieldRow(new MultiKeyField(
            name: 'p2p_add',
            label: 'С этим товаром покупают',
            values: $values,
            relations: 'catalog_p2p_add',
            key: $key,
            relationKey: $relationKey,
            parentKey: $parentKey,
        ), 'catalog_product'));
        $field->tablePk = 'product_id';

        return $field;
    }

    private function makeRelation(): FieldRelation
    {
        $field = new FieldRelation(Repository::toFieldRow(new FieldDefinition(
            name: 'p2p',
            class: FieldRelation::class,
            label: 'Другие цвета',
            params: ['relation' => 'catalog_p2p', 'left' => 'left_id', 'right' => 'right_id', 'name' => 'name'],
        ), 'catalog_product'));
        $field->tablePk = 'product_id';

        return $field;
    }
}
