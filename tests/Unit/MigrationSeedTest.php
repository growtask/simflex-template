<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DB\Adapter;
use Simflex\Core\DB\Schema;

/**
 * Migrations write their initial data themselves: Schema::seed() runs once the tables are created
 */
class MigrationSeedTest extends Unit
{
    private ?Adapter $dbBackup = null;
    private mixed $configBackup = null;

    protected function _before(): void
    {
        $prop = new \ReflectionProperty(DB::class, 'db');
        $this->dbBackup = $prop->isInitialized() ? $prop->getValue() : null;
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        // DB::query() asks the config for devMode (profiler)
        Container::set('config', (object)['devMode' => false]);
    }

    protected function _after(): void
    {
        if ($this->dbBackup) {
            (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $this->dbBackup);
        }
        Container::set('config', $this->configBackup);
    }

    private function schema(array &$queries): Schema
    {
        $db = $this->createMock(Adapter::class);
        $db->method('query')->willReturnCallback(function (string $q) use (&$queries) {
            $queries[] = str_starts_with($q, 'CREATE') ? 'CREATE' : $q;
            return true;
        });
        (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $db);

        // without reload(): the existing tables don't matter here
        return (new \ReflectionClass(Schema::class))->newInstanceWithoutConstructor();
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

        // the data is written once
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
}
