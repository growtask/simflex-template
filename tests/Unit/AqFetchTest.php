<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Core\DB;
use Simflex\Core\DB\Adapter;
use Simflex\Core\DB\AQ;

/**
 * AQ::fetchColumn() and AQ::fetchScalar() on the rows given by a mocked adapter
 */
class AqFetchTest extends Unit
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

    private function rows(array $rows): void
    {
        $db = $this->createMock(Adapter::class);
        $db->method('query')->willReturn(true);
        $db->method('fetch')->willReturnCallback(function () use (&$rows) {
            return array_shift($rows) ?? false;
        });
        (new \ReflectionProperty(DB::class, 'db'))->setValue(null, $db);
    }

    public function testFetchColumnByName(): void
    {
        $this->rows([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']]);
        $this->assertSame(['a', 'b'], (new AQ())->from('t')->fetchColumn('name'));
    }

    public function testFetchColumnByIndex(): void
    {
        $this->rows([['id' => 1, 'name' => 'a'], ['id' => 2, 'name' => 'b']]);
        $this->assertSame([1, 2], (new AQ())->from('t')->fetchColumn());
    }

    public function testFetchColumnOnEmptyResult(): void
    {
        $this->rows([]);
        $this->assertSame([], (new AQ())->from('t')->fetchColumn('name'));
    }

    public function testFetchColumnThrowsOnMissingColumn(): void
    {
        $this->rows([['id' => 1]]);
        $this->expectException(\Exception::class);
        (new AQ())->from('t')->fetchColumn('name');
    }

    public function testFetchScalarOnEmptyResultReturnsNull(): void
    {
        $this->rows([]);
        $this->assertNull((new AQ())->from('t')->fetchScalar('name'));
    }

    public function testFetchScalarReturnsFirstRowValue(): void
    {
        $this->rows([['id' => 7, 'name' => 'a'], ['id' => 8, 'name' => 'b']]);
        $this->assertSame('a', (new AQ())->from('t')->fetchScalar('name'));
    }
}
