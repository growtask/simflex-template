<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\System\ModelBase;
use Tests\Support\MockDb;

class ModelIdentityTest extends Unit
{
    use MockDb;

    public function testPrimaryKeyNamedIdSurvivesInsertAndNextSave(): void
    {
        $this->mockRows([], $queries)->method('insertId')->willReturn('7');
        $model = new class () extends ModelBase {
            protected static string $table = 'identity_probe';
            protected static string $primaryKeyName = 'id';
        };
        $this->assertTrue($model->save(['name' => 'First']));
        $this->assertSame(7, $model->getId());
        $this->assertSame(7, $model->toArray()['id']);
        $this->assertTrue($model->save(['name' => 'Second']));
        $this->assertSame(7, $model->getId());
        $updates = array_filter($queries, fn(array $query) => str_starts_with(strtolower($query[0]), 'update '));
        $inserts = array_filter($queries, fn(array $query) => str_starts_with(strtolower($query[0]), 'insert '));
        $this->assertCount(1, $updates);
        $this->assertCount(1, $inserts);
    }

    /**
     * Таблица без AUTO_INCREMENT: insertId() - 0, ключ из данных модели остаётся
     */
    public function testProvidedKeyKeptWithoutAutoIncrement(): void
    {
        $this->mockRows([], $queries)->method('insertId')->willReturn('0');
        $class = get_class(new class () extends ModelBase {
            protected static string $table = 'identity_probe';
            protected static string $primaryKeyName = 'code_id';
        });

        $model = $class::insert(['code_id' => 42, 'name' => 'Manual']);
        $this->assertNotNull($model);
        $this->assertSame(42, $model->getId());
        $this->assertSame(42, $model->toArray()['code_id']);
    }
}
