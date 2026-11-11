<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\DB\Schema\Table;
use Tests\Support\MockDb;

final class SchemaElementsTest extends Unit
{
    use MockDb;

    public function testEveryUnnamedIndexAndConstraintSurvivesFastCreation(): void
    {
        $this->mockDb();
        $table = new Table('schema_elements_test');
        $table->id('id');
        for ($i = 0; $i < 2000; $i++) {
            $table->addIndex()->index('column_' . $i);
            $table->addConstraint()->unique('column_' . $i);
        }
        $sql = $table->toString();
        $this->assertSame(2000, substr_count($sql, 'INDEX '));
        $this->assertSame(2000, substr_count($sql, 'UNIQUE '));
    }
}
