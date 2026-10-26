<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Admin\Handler;
use Simflex\Admin\Handlers;
use Simflex\Admin\Table\Repository;

/**
 * Обработчики разделов админки, объявленные в коде (Admin/handlers.php ядра и сайта)
 */
class HandlersTest extends Unit
{
    public function testEveryHandlerIsHandlerOrTable(): void
    {
        foreach (Repository::getHandlerClasses() as $class) {
            $this->assertTrue(is_subclass_of($class, Handler::class), $class);
        }
        $this->assertContains(Handlers\Privilege::class, Repository::getHandlerClasses());
        // раздел без таблицы — не таблица
        $this->assertNotContains(Handlers\Privilege::class, Repository::getTableClasses());
        $this->assertNull(Repository::getTableClass('privilege'));
    }

    public function testTableNameFromModel(): void
    {
        $this->assertSame('user_role', Handlers\UserRole::getTableName());
        $this->assertSame(Handlers\UserRole::class, Repository::getTableClass('user_role'));
        $this->assertSame(Handlers\UserRole::class, Repository::getTableClasses()['user_role']);
        foreach (Repository::getTableClasses() as $name => $class) {
            $this->assertNotEmpty($class::getFields(), "$name: no fields");
            $this->assertSame($name, $class::getModel()::getTableName());
        }
        $this->assertSame('cron', Handlers\Cron::getFieldRow('job')['table']);
        $this->assertSame([], Handlers\Cron::getFieldRow('no_such_field'));

        // таблица без объявленной модели
        $this->expectException(\LogicException::class);
        NoModelTable::getModel();
    }

    /**
     * width '1' — колонка, занимающая остаток списка: две такие колонки делят его неравномерно
     */
    public function testEveryTableHasAtMostOneStretchingColumn(): void
    {
        foreach (Repository::getTableClasses() as $name => $class) {
            $stretching = [];
            foreach ($class::getFields() as $field) {
                if ((string)$field->width === '1') {
                    $stretching[] = $field->name;
                }
            }
            $this->assertLessThanOrEqual(1, count($stretching), "$name: width '1' in " . implode(', ', $stretching));
        }
    }
}

abstract class NoModelTable extends \Simflex\Admin\Table\Table
{
}
