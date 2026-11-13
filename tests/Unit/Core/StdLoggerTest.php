<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;

/**
 * StdLogger: вывод не в терминал (журнал контейнера, перенаправление в файл) — без кодов ANSI
 */
class StdLoggerTest extends Unit
{
    public function testPipedOutputHasNoAnsiCodes(): void
    {
        $code = 'require "vendor/autoload.php"; '
            . '(new Simflex\Core\Logger\StdLogger())->error("Cron job {job} failed", ["job" => "team_remind"]);';
        $process = proc_open([PHP_BINARY, '-r', $code], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, dirname(__DIR__, 3));
        $this->assertIsResource($process);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $this->assertSame(0, proc_close($process));
        $this->assertMatchesRegularExpression('/^\d\d:\d\d:\d\d \[error\] Cron job team_remind failed\n\z/', $output);
    }
}
