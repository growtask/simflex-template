<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\System\ModelBase;

final class ModelIssetTest extends Unit
{
    public function testNullCoalescingAndIssetLoadVirtualPropertiesAndCamelCaseColumns(): void
    {
        $model = new class () extends ModelBase {
            protected static string $table = 'test_model';
            protected static string $primaryKeyName = 'id';
            public int $calls = 0;

            protected function offsetGetRelated(): object
            {
                $this->calls++;
                return (object)['name' => 'Relation'];
            }
        };
        $model->fill(['id' => 1, 'type_id' => 0, 'is_off' => false, 'empty_string' => '', 'nullable' => null]);
        $this->assertSame('Relation', $model->related?->name ?? 'fallback');
        $this->assertTrue(isset($model->related));
        $this->assertSame(1, $model->calls);
        $this->assertTrue(isset($model->typeId));
        $this->assertSame(0, $model->typeId ?? 7);
        $this->assertSame(false, $model['isOff'] ?? true);
        $this->assertSame('', $model->emptyString ?? 'fallback');
        $this->assertFalse(isset($model->nullable));
        $this->assertSame('fallback', $model->unknown ?? 'fallback');
    }
}
