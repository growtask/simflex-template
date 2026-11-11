<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\DB\Schema;
use Simflex\Core\DB\Schema\Column;
use Simflex\Core\DB\Schema\Table;
use Tests\Support\MockDb;

/**
 * Построитель схемы миграций; начальные данные пишет сама миграция (Schema::seed())
 */
class SchemaTest extends Unit
{
    use MockDb;

    public function testColumnDefaults(): void
    {
        $sql = static fn($default) => (new Column('c'))->dataType('int')->setDefault($default)->autoIncrement()->toString();
        $this->assertStringContainsString('DEFAULT NULL AUTO_INCREMENT', $sql(null));
        $this->assertStringContainsString('DEFAULT 0 AUTO_INCREMENT', $sql(false));
        $this->assertStringContainsString('DEFAULT 1 AUTO_INCREMENT', $sql(true));
        $this->assertStringContainsString('DEFAULT 5 AUTO_INCREMENT', $sql(5));
        $this->assertStringContainsString('DEFAULT 1.5 AUTO_INCREMENT', $sql(1.5));
    }

    public function testCreateTable(): void
    {
        $table = new Table('typing_test');
        $table->integer('id')->primaryKey()->autoIncrement();
        $this->assertStringContainsString('CREATE TABLE `typing_test`', $table->toString());
        $this->assertStringContainsString('AUTO_INCREMENT', $table->toString());
    }

    public function testDataIsWrittenAfterTheTables(): void
    {
        $queries = [];
        $s = $this->schema($queries);
        $s->seed(function () use (&$queries) {
            $queries[] = 'seed';
        });
        $s->createTable('blog', fn(Schema\Table $c) => $c->id('blog_id'));

        $this->assertSame([], $queries, 'nothing runs before commit');
        $this->assertTrue($s->commit());
        $this->assertSame(['CREATE', 'seed'], $queries);

        // данные пишутся один раз
        $this->assertTrue($s->commit());
        $this->assertSame(['CREATE', 'seed'], $queries);
    }

    public function testFailedDataFailsTheCommit(): void
    {
        $queries = [];
        $s = $this->schema($queries);
        $s->seed(fn() => false);
        $this->assertFalse($s->commit());

        $s->seed(fn() => throw new \RuntimeException('duplicate'));
        $this->assertFalse($s->commit());
    }

    private function schema(array &$queries): Schema
    {
        $this->mockDb()->method('query')->willReturnCallback(function (string $q) use (&$queries) {
            $queries[] = str_starts_with($q, 'CREATE') ? 'CREATE' : $q;
            return true;
        });

        // без reload(): существующие таблицы здесь не важны
        return (new \ReflectionClass(Schema::class))->newInstanceWithoutConstructor();
    }
}
