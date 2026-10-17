<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Admin\Structure\Repository;

/**
 * width '1' is the column taking the rest of the list: two such columns share it unevenly
 */
class TableWidthTest extends Unit
{
    public function testEveryTableHasAtMostOneStretchingColumn(): void
    {
        $root = dirname(__DIR__, 2);
        defined('SF_CORE_ROOT_PATH') || require_once $root . '/vendor/growtask/simflex/src/constants.php';
        defined('SF_ROOT_PATH') || require_once $root . '/constants.php';

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
