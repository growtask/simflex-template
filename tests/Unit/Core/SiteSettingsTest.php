<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\SiteSettings;
use Tests\Support\MockDb;

/**
 * Настройки сайта: загрузка один раз, подстановка значений, создание отсутствующей настройки
 */
class SiteSettingsTest extends Unit
{
    use MockDb;

    protected function _before(): void
    {
        SiteSettings::reset();
    }

    protected function _after(): void
    {
        SiteSettings::reset();
    }

    public function testValuesAreLoadedOnceAndInterpolated(): void
    {
        $this->mockRows([['alias' => 'greeting', 'value' => 'Hello {name}'], ['alias' => 'phone', 'value' => '123']], $queries);

        $this->assertSame('Hello John', SiteSettings::get('greeting', '', ['name' => 'John']));
        $this->assertSame('123', SiteSettings::get('phone'));
        $this->assertSame(['greeting' => 'Hello {name}', 'phone' => '123'], SiteSettings::getAll());
        $this->assertCount(1, $queries);
    }

    public function testMissingSettingIsCreatedWithDefault(): void
    {
        $this->mockRows([], $queries)->method('insertId')->willReturn('1');

        $this->assertSame(5, SiteSettings::get('per_page', 5));
        $insert = array_values(array_filter($queries, fn(array $q) => stripos(ltrim($q[0]), 'insert') === 0));
        $this->assertCount(1, $insert);
        $this->assertContains('per_page', $insert[0][1]);
        $this->assertContains('int', $insert[0][1]);
    }
}
