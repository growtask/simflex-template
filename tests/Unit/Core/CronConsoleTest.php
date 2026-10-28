<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Cron\ConsoleCron;

/**
 * ./sf cron/install, cron/uninstall: строка сайта в crontab текущего пользователя. crontab подменён скриптом,
 * который хранит таблицу в файле
 */
class CronConsoleTest extends Unit
{
    protected string $dir = '';
    protected string $path = '';

    protected function _before(): void
    {
        $this->dir = sys_get_temp_dir() . '/crontab-test-' . uniqid();
        mkdir($this->dir);
        file_put_contents($this->dir . '/crontab', <<<'SH'
            #!/bin/sh
            TAB="$(dirname "$0")/tab"
            if [ "$1" = "-l" ]; then
                [ -f "$TAB" ] || { echo "no crontab for user" >&2; exit 1; }
                cat "$TAB"
            else
                cp "$1" "$TAB"
            fi
            SH);
        chmod($this->dir . '/crontab', 0755);
        $this->path = (string)getenv('PATH');
        putenv('PATH=' . $this->dir . ':' . $this->path);
    }

    protected function _after(): void
    {
        putenv('PATH=' . $this->path);
        array_map(unlink(...), glob($this->dir . '/*'));
        rmdir($this->dir);
    }

    public function testInstallKeepsOtherLinesAndIsIdempotent(): void
    {
        file_put_contents($this->dir . '/tab', "0 3 * * * backup.sh\n");
        $line = ConsoleCron::getLine();
        $this->assertStringContainsString('sf cron/run', $line);

        ob_start();
        try {
            (new ConsoleCron())->install();
            (new ConsoleCron())->install();
        } finally {
            ob_end_clean();
        }
        $this->assertSame("0 3 * * * backup.sh\n$line\n", file_get_contents($this->dir . '/tab'));

        ob_start();
        try {
            (new ConsoleCron())->uninstall();
        } finally {
            ob_end_clean();
        }
        $this->assertSame("0 3 * * * backup.sh\n", file_get_contents($this->dir . '/tab'));
    }

    public function testInstallIntoEmptyCrontab(): void
    {
        ob_start();
        try {
            (new ConsoleCron())->install();
        } finally {
            ob_end_clean();
        }
        $this->assertSame(ConsoleCron::getLine() . "\n", file_get_contents($this->dir . '/tab'));
    }
}
