<?php

declare(strict_types=1);

namespace Tests\Support\Extension;

use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Extension;

/**
 * Empties today's log before every test, so a test judges only what it wrote itself
 * (not leftovers from earlier tests or manual work, like failed console commands).
 * The file is truncated, not deleted: the app keeps writing to the same file with the same owner
 */
class LogCleaner extends Extension
{
    public static array $events = [
        Events::TEST_BEFORE => 'clearLog',
    ];

    public function clearLog(TestEvent $e): void
    {
        $file = 'log/' . date('Y-m-d') . '.log';
        if (is_file($file)) {
            file_put_contents($file, '');
        }
    }
}
