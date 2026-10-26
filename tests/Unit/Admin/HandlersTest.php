<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Admin\Base;
use Simflex\Admin\Structure\Handler;
use Simflex\Admin\Structure\Handlers;
use Simflex\Admin\Structure\Repository;

/**
 * The admin section handlers declared in code (Admin/Structure/handlers.php of the core and of the site)
 */
class HandlersTest extends Unit
{
    public function testEveryHandlerIsHandlerOrTable(): void
    {
        foreach (Repository::handlerClasses() as $name => $class) {
            $this->assertTrue(is_subclass_of($class, Handler::class), "$name: $class");
        }
        // a section without a table isn't a table: no table name, no table data
        $this->assertSame('', Repository::tableName(Handlers\Privilege::class));
        $this->assertSame([], Repository::table('privilege'));
        $this->assertSame('user_role', Repository::tableName(Handlers\UserRole::class));
        $this->assertSame(Base\Privileges::class, (new Handlers\Privilege())->class);
    }

    /**
     * width '1' is the column taking the rest of the list: two such columns share it unevenly
     */
    public function testEveryTableHasAtMostOneStretchingColumn(): void
    {
        foreach (Repository::tableClasses() as $name => $class) {
            $stretching = [];
            foreach ((new $class())->fields as $field) {
                if ((string)($field->params['main']->width ?? '') === '1') {
                    $stretching[] = $field->name;
                }
            }
            $this->assertLessThanOrEqual(1, count($stretching), "$name: width '1' in " . implode(', ', $stretching));
        }
    }
}
