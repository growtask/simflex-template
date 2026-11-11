<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\System\Settings;
use Tests\Support\MockDb;

/**
 * Настройки сайта: загрузка один раз, подстановка значений, создание отсутствующей настройки
 */
class SiteSettingsTest extends Unit
{
    use MockDb;

    protected function _before(): void
    {
        Settings::reset();
    }

    protected function _after(): void
    {
        Settings::reset();
    }

    public function testValuesAreLoadedOnceAndInterpolated(): void
    {
        $this->mockRows([['alias' => 'greeting', 'value' => 'Hello {name}'], ['alias' => 'phone', 'value' => '123']], $queries);

        $this->assertSame('Hello John', Settings::get('greeting', '', ['name' => 'John']));
        $this->assertSame('123', Settings::get('phone'));
        $this->assertSame(['greeting' => 'Hello {name}', 'phone' => '123'], Settings::getAll());
        $this->assertCount(1, $queries);
    }

    public function testMissingSettingIsCreatedWithDefault(): void
    {
        $this->mockRows([], $queries)->method('insertId')->willReturn('1');

        $this->assertSame(5, Settings::get('per_page', 5));
        $insert = array_values(array_filter($queries, fn(array $q) => stripos(ltrim($q[0]), 'insert') === 0));
        $this->assertCount(1, $insert);
        $this->assertContains('per_page', $insert[0][1]);
        $this->assertContains('int', $insert[0][1]);
    }
}
