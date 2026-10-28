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

/**
 * Поля админки
 */
class FieldsTest extends Unit
{
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
