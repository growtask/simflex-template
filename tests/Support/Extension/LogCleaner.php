<?php

declare(strict_types=1);

namespace Tests\Support\Extension;

use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Extension;

/**
 * Очищает сегодняшний лог перед каждым тестом, чтобы тест оценивал только то, что записал сам
 * (а не остатки прошлых тестов или ручной работы, вроде упавших консольных команд).
 * Файл обрезается, а не удаляется: сайт продолжает писать в тот же файл с тем же владельцем
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
