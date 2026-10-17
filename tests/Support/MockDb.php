<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DB\Adapter;

/**
 * Unit tests without a database: DB::query() and the rest go to a mocked adapter.
 * The real adapter and the config are put back after every test
 */
trait MockDb
{
    private ?Adapter $dbBackup = null;
    private mixed $configBackup = null;

    #[Before]
    protected function backupDb(): void
    {
        $prop = new \ReflectionProperty(DB::class, 'db');
        $this->dbBackup = $prop->isInitialized() ? $prop->getValue() : null;
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;

        // DB::query() asks the config for devMode (the profiler wants the debug bar then)
        $config = $this->configBackup ? clone $this->configBackup : new \stdClass();
        $config->devMode = false;
        Container::set('config', $config);
    }

    #[After]
    protected function restoreDb(): void
    {
        if ($this->dbBackup) {
            (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $this->dbBackup);
        }
        Container::set('config', $this->configBackup);
    }

    protected function mockDb(): Adapter&MockObject
    {
        $db = $this->createMock(Adapter::class);
        $db->method('escape')->willReturnArgument(0);
        (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $db);
        return $db;
    }

    /**
     * Every query succeeds and gives these rows; the queries are collected in $queries
     */
    protected function mockRows(array $rows, ?array &$queries = null): Adapter&MockObject
    {
        $queries = [];
        $db = $this->mockDb();
        $db->method('query')->willReturnCallback(function (string $q, array $params = []) use (&$queries) {
            $queries[] = [$q, $params];
            return true;
        });
        $db->method('fetch')->willReturnCallback(function () use (&$rows) {
            return array_shift($rows) ?? false;
        });
        return $db;
    }
}
