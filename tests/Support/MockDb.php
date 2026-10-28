<?php

declare(strict_types=1);

namespace Tests\Support;

use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use Simflex\Core\Container;
use Simflex\DB\DB;
use Simflex\DB\Adapter;

/**
 * Unit-тесты без базы: DB::query() и остальное идут в мок адаптера.
 * Настоящий адаптер и конфиг возвращаются после каждого теста
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

        // DB::query() спрашивает у конфига devMode (профилировщику тогда нужен debug bar)
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
     * Каждый запрос успешен и возвращает эти записи; запросы собираются в $queries
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
