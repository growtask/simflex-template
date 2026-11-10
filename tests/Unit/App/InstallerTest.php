<?php

declare(strict_types=1);

namespace Tests\Unit\App;

use Codeception\Test\Unit;

final class InstallerTest extends Unit
{
    public function testUnavailableDatabaseFailsWithoutChangingEnvironment(): void
    {
        $root = dirname(__DIR__, 3);
        $before = file_get_contents($root . '/.env');
        $process = proc_open(
            [PHP_BINARY, 'sf', 'install/install'],
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root,
            array_replace(getenv(), ['DB_HOST' => '127.0.0.1', 'DB_PORT' => '1', 'DEVELOPER' => '0'])
        );
        try {
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $status = proc_close($process);
            $process = null;
            $this->assertNotSame(0, $status, $output);
            $this->assertSame($before, file_get_contents($root . '/.env'));
        } finally {
            if (is_resource($process)) {
                proc_terminate($process);
                proc_close($process);
            }
        }
    }
}
