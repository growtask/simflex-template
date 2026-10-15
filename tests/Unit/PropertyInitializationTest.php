<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Admin\Base;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldTime;
use Simflex\Core\Cookie\CookieItem;
use Simflex\Core\DB\AQ;
use Simflex\Core\DB\JoinClause;
use Simflex\Core\DB\Schema\Table;
use Simflex\Core\TimeDiff;

class PropertyInitializationTest extends Unit
{
    public function testAdminIdsAreInitializedAndNormalizedByConstructor(): void
    {
        $get = $_GET;
        $post = $_POST;
        try {
            $_GET['ids'] = '1,0,2,invalid,3';
            unset($_POST['group_ids']);
            $make = static fn () => new class extends Base {
                protected function initTableName(): void {}
                protected function initTableData(): void {}
            };
            $this->assertSame('1,2,3', $make()->ids);
            $_POST['group_ids'] = '4,5';
            $this->assertSame('4,5', $make()->ids);
            unset($_GET['ids'], $_POST['group_ids']);
            $this->assertSame('', $make()->ids);
        } finally {
            $_GET = $get;
            $_POST = $post;
        }
    }

    public function testQueryBuildsWithoutOptionalClauses(): void
    {
        $this->assertSame('SELECT * FROM `content`', trim((new AQ())->from('content')->build()));
        $this->assertSame('INNER JOIN `category` USING(`category_id`)', (new JoinClause('category', 'category_id'))->toSql('content'));
    }

    public function testQueryWithoutTableReportsValidationError(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Bad from statement');
        (new AQ())->build();
    }

    public function testFieldInheritancePreservesNumericDefaultsAndFractionalWidths(): void
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

    public function testConstructorInitializedValueObjects(): void
    {
        $cookie = new CookieItem('typing_test_cookie', 'value');
        $this->assertSame('value', $cookie->getValue());
        $this->assertNull((new CookieItem('typing_test_absent_cookie'))->getValue());
        $interval = new \DateInterval('P2DT3H');
        $diff = new TimeDiff($interval);
        $this->assertSame($interval, $diff->getRawDateInterval());
        $this->assertSame('2 03', $diff->format('%d %H'));
    }

    public function testSchemaBuilderInitializesParameterObjects(): void
    {
        $table = new Table('typing_test');
        $table->integer('id')->primaryKey()->autoIncrement();
        $this->assertStringContainsString('CREATE TABLE `typing_test`', $table->toString());
        $this->assertStringContainsString('AUTO_INCREMENT', $table->toString());
    }
}
