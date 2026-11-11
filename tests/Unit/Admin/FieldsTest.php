<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\Fields\FieldBool;
use Simflex\Core\Admin\Fields\FieldDate;
use Simflex\Core\Admin\Fields\FieldDateTime;
use Simflex\Core\Admin\Fields\FieldDouble;
use Simflex\Core\Admin\Fields\FieldInt;
use Simflex\Core\Admin\Fields\FieldNPP;
use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Fields\FieldTable;
use Simflex\Core\Admin\Fields\FieldTime;
use Simflex\Core\System\Container;

/**
 * Поля админки
 */
class FieldsTest extends Unit
{
    private mixed $configBackup;

    protected function _before(): void
    {
        // ячейки и фильтры выводят компоненты Layout: renderIcon() берёт тему из конфига
        $this->configBackup = Container::isSet('config') ? Container::get('config') : null;
        Container::set('config', (object)['theme' => 'default', 'devMode' => false]);
    }

    protected function _after(): void
    {
        Container::set('config', $this->configBackup);
    }

    public function testListCellsAndFiltersAreReturned(): void
    {
        $string = new FieldString(['name' => 'title', 'label' => 'Title', 'table' => 't']);
        $string->filter = true;
        $bool = new FieldBool(['name' => 'active', 'label' => 'Active', 'table' => 't']);

        $this->expectOutputString('');
        $this->assertStringContainsString('Hello', $string->show(['title' => 'Hello']));
        $this->assertStringContainsString('filter[title]', $string->filter('x'));
        $this->assertNotSame('', $bool->show(['active' => 1]));
        $this->assertSame('', $bool->filter('1'));
        // ячейка bool — переключатель, который сохраняет значение сразу
        $this->assertStringContainsString('data-sf-bool-change', $bool->show(['active' => 1]));
        $bool->filter = true;
        $this->assertStringContainsString('name="filter[active]"', $bool->filter('1'));
        $this->assertSame('Да', $bool->getFilterLabel('1'));
        $this->assertSame('Нет', $bool->getFilterLabel('0'));
        // меню фильтра в шапке: у bool — значения переключателями, у текста — поле поиска
        $this->assertSame(['1' => 'Да', '0' => 'Нет'], $bool->getFilterOptions());
        $this->assertNull($string->getFilterOptions());
        $this->assertStringContainsString('filter-search', $string->filter('x'));
        $this->assertStringNotContainsString('data-sf-digits', $string->filter('x'));
        // фильтр числа — только цифры
        $int = new FieldInt(['name' => 'id', 'label' => 'ID', 'table' => 't']);
        $int->filter = true;
        $html = $int->filter('5');
        $this->assertStringContainsString('data-sf-digits', $html);
        $this->assertStringContainsString('inputmode="numeric"', $html);
    }

    public function testDateRangeFilterValue(): void
    {
        $range = \Simflex\Core\Admin\Fields\DateRange::class;
        $this->assertSame(['2026-11-01', '2026-11-30'], $range::parse('2026-11-01|2026-11-30'));
        $this->assertSame(['2026-11-01', null], $range::parse('2026-11-01|'));
        $this->assertSame([null, '2026-11-30'], $range::parse('|2026-11-30'));
        // не дата и несуществующий день — границы нет
        $this->assertSame([null, null], $range::parse("2026-02-30|x' OR 1"));
        $this->assertSame('с 01.11.2026', $range::getLabel('2026-11-01|'));
        $this->assertSame('по 30.11.2026', $range::getLabel('|2026-11-30'));
        $this->assertSame('', $range::getLabel('abc'));

        $date = new FieldDate(['name' => 'date', 'label' => 'Дата', 'table' => 't']);
        $this->assertSame('', $date->filter(''));
        $date->filter = true;
        $html = $date->filter('2026-11-01|');
        $this->assertStringContainsString('data-sf-date-range="to"', $html);
        $this->assertStringContainsString('name="filter[date]" value="2026-11-01|"', $html);
    }

    public function testNumericDefaultsAndFractionalWidths(): void
    {
        $row = ['name' => 'value', 'label' => 'Value', 'table' => 'content', 'params' => ['main' => ['width' => 120.5]]];
        foreach ([FieldInt::class, FieldBool::class] as $class) {
            $field = new $class($row);
            $this->assertSame(0, $field->defaultValue);
            $this->assertSame(120.5, $field->width);
        }
        $field = new FieldTime($row);
        $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $field->defaultValue);
        $this->assertStringContainsString('12:34', $field->input('12:34:56'));
    }

    public function testEmptyNullableFieldsGiveNull(): void
    {
        $row = ['name' => 'f', 'label' => 'F', 'table' => 'content', 'params' => ['main' => ['e2n' => 1]]];
        foreach ([FieldDate::class, FieldDateTime::class, FieldDouble::class, FieldNPP::class, FieldTime::class] as $class) {
            $field = new $class($row);
            $field->setPosted(['f' => '']);
            $this->assertNull($field->getPOST(), $class);
        }
    }

    public function testTableValueIsJson(): void
    {
        $this->assertSame('{"s":["a"],"v":["б"]}', FieldTable::makeValue(['a'], ['б']));
        $this->expectException(\JsonException::class);
        FieldTable::makeValue([], ["\xB1\x31"]);
    }
}
