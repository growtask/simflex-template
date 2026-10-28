<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\DB\Adapter;
use Simflex\Core\DB\DB;
use Tests\Support\MockDb;

/**
 * DB::transaction(): фиксация, откат при исключении, вложенные вызовы
 */
class TransactionTest extends Unit
{
    use MockDb;

    /** @var list<string> вызовы транзакций адаптера */
    protected array $calls = [];

    protected function mockTransactions(): Adapter
    {
        $active = false;
        $db = $this->mockDb();
        $db->method('inTransaction')->willReturnCallback(function () use (&$active) {
            return $active;
        });
        $db->method('beginTransaction')->willReturnCallback(function () use (&$active) {
            $active = true;
            $this->calls[] = 'begin';
        });
        $db->method('commitTransaction')->willReturnCallback(function () use (&$active) {
            $active = false;
            $this->calls[] = 'commit';
        });
        $db->method('rollbackTransaction')->willReturnCallback(function () use (&$active) {
            $active = false;
            $this->calls[] = 'rollback';
        });

        return $db;
    }

    public function testResultIsCommitted(): void
    {
        $this->mockTransactions();

        $this->assertSame(42, DB::transaction(fn() => 42));
        $this->assertSame(['begin', 'commit'], $this->calls);
    }

    public function testExceptionRollsBack(): void
    {
        $this->mockTransactions();

        try {
            DB::transaction(function () {
                throw new \RuntimeException('boom');
            });
            $this->fail('exception is rethrown');
        } catch (\RuntimeException $e) {
            $this->assertSame('boom', $e->getMessage());
        }
        $this->assertSame(['begin', 'rollback'], $this->calls);
    }

    public function testNestedCallRunsInOuterTransaction(): void
    {
        $this->mockTransactions();

        try {
            DB::transaction(function () {
                DB::transaction(fn() => 'inner');
                throw new \RuntimeException('outer fails');
            });
        } catch (\RuntimeException) {
        }
        $this->assertSame(['begin', 'rollback'], $this->calls);
    }
}
