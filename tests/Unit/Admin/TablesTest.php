<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Admin\Structure\Repository;

/**
 * The admin tables declared in code (Admin/Structure/tables.php of the core and of the site)
 */
class TablesTest extends Unit
{
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
