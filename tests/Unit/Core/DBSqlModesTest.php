<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\DB\DB;

final class DBSqlModesTest extends Unit
{
    public function testModeIsScopedAndRestoredAfterNestedCallsAndExceptions(): void
    {
        $database = new class () extends DB {
            public static string $mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE';

            public static function result(string $q, int|string $field = '', array $params = []): mixed
            {
                return self::$mode;
            }

            public static function query(string $q, array $params = []): mixed
            {
                self::$mode = $params[0];
                return true;
            }
        };
        $previous = $database::$mode;
        try {
            $database::withoutSqlModes(['NO_ZERO_DATE'], function () use ($database): void {
                $this->assertSame('STRICT_TRANS_TABLES,NO_ZERO_IN_DATE', $database::$mode);
                $value = $database::withoutSqlModes(['NO_ZERO_IN_DATE'], function () use ($database): int {
                    $this->assertSame('STRICT_TRANS_TABLES', $database::$mode);
                    return 7;
                });
                $this->assertSame(7, $value);
                $this->assertSame('STRICT_TRANS_TABLES,NO_ZERO_IN_DATE', $database::$mode);
                throw new \DomainException('fixture');
            });
            $this->fail('Исключение должно выйти из колбэка');
        } catch (\DomainException $exception) {
            $this->assertSame('fixture', $exception->getMessage());
        } finally {
            $this->assertSame($previous, $database::$mode);
        }
    }
}
