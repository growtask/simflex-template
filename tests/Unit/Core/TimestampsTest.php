<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\DB\Schema\Table;
use Simflex\Core\Models\Log;
use Simflex\Core\Models\ModuleItem;
use Tests\Support\MockDb;

/**
 * Колонки time_create и time_update: добавление в таблицу миграции и заполнение в ModelBase
 */
class TimestampsTest extends Unit
{
    use MockDb;

    private function createSql(callable $fn): string
    {
        $this->mockDb();
        $table = new Table('t');
        $fn($table);
        $table->addTimestamps();
        return $table->toString();
    }

    public function testTableGetsTimeColumnsByDefault(): void
    {
        $sql = $this->createSql(fn(Table $c) => $c->id('t_id'));
        $this->assertStringContainsString('`time_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP', $sql);
        $this->assertStringContainsString('`time_update` timestamp NULL DEFAULT NULL', $sql);
    }

    public function testTableWithoutTimestamps(): void
    {
        $sql = $this->createSql(function (Table $c) {
            $c->id('t_id');
            $c->noTimestamps();
        });
        $this->assertStringNotContainsString('time_', $sql);
    }

    public function testTableWithOneTimeColumn(): void
    {
        $sql = $this->createSql(function (Table $c) {
            $c->id('t_id');
            $c->timestampsOnlyTimeCreate();
        });
        $this->assertStringContainsString('`time_create`', $sql);
        $this->assertStringNotContainsString('time_update', $sql);

        $sql = $this->createSql(function (Table $c) {
            $c->id('t_id');
            $c->timestampsOnlyTimeUpdate();
        });
        $this->assertStringNotContainsString('time_create', $sql);
        $this->assertStringContainsString('`time_update`', $sql);
    }

    public function testOwnTimeColumnIsKept(): void
    {
        $sql = $this->createSql(function (Table $c) {
            $c->id('t_id');
            $c->dateTime('time_create');
        });
        $this->assertSame(1, substr_count($sql, 'time_create'));
        $this->assertStringContainsString('`time_update`', $sql);
    }

    public function testInsertAddsTimeColumns(): void
    {
        $this->mockRows([], $queries)->method('insertId')->willReturn('7');
        $this->assertTrue((new ModuleItem())->save(['name' => 'N']));
        $this->assertSame(
            [['insert  into `module_item` (`name`, `time_create`, `time_update`) values (?, NOW(), NOW())', ['N']]],
            $queries
        );
    }

    public function testExplicitTimeIsKept(): void
    {
        $this->mockRows([], $queries)->method('insertId')->willReturn('7');
        $this->assertTrue((new ModuleItem())->save(['name' => 'N', 'time_create' => '2020-01-01 00:00:00']));
        $this->assertStringContainsString('(`name`, `time_create`, `time_update`) values (?, ?, NOW())', $queries[0][0]);
    }

    public function testUpdateSetsTimeUpdate(): void
    {
        $this->mockRows([], $queries);
        $model = new ModuleItem();
        $model->fill(['item_id' => 5, 'name' => 'Old', 'time_update' => null]);
        $this->assertTrue($model->update(['name' => 'New']));
        $this->assertStringContainsString('set `name` = ?, `time_update` = NOW() where', $queries[0][0]);
    }

    public function testBulkUpdateAndUpsertTouchTimeUpdate(): void
    {
        $this->mockRows([], $queries);
        ModuleItem::bulkUpdate(['name' => 'N'], ['item_id' => 3]);
        $this->assertStringContainsString('`name` = :set_0, `time_update` = NOW()', $queries[0][0]);

        ModuleItem::upsert(['item_id' => 3, 'name' => 'N'], ['name']);
        $this->assertSame(
            'insert into `module_item` (`item_id`, `name`, `time_create`, `time_update`) values (?, ?, NOW(), NOW())'
            . ' on duplicate key update `name` = VALUES(`name`), `time_update` = NOW()',
            $queries[1][0]
        );
    }

    public function testModelWithNullConstantsHasNoTimeColumns(): void
    {
        $this->mockRows([], $queries);
        Log::bulkUpdate(['action' => 'N'], ['log_id' => 3]);
        $this->assertStringNotContainsString('time_', $queries[0][0]);
    }
}
