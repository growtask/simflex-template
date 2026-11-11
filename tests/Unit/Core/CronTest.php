<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Cron\Cron;
use Simflex\Core\Cron\CronJob;
use Simflex\Core\Cron\CronRegistry;
use Simflex\Core\System\ExtensionConfig;
use Tests\Support\MockDb;

/**
 * Запуск заданий cron по расписанию
 */
class CronTest extends Unit
{
    use MockDb;

    protected function _after(): void
    {
        CronRegistry::reset();
        ExtensionConfig::reset();
    }

    public function testTimingMatchesGivenTime(): void
    {
        $cron = new class () extends Cron {
            public function isDue(string $timing, int $time): bool
            {
                return $this->testTiming($timing, $time);
            }
        };
        // 2026-10-31 (суббота) 10:15
        $time = mktime(10, 15, 0, 10, 31, 2026);

        $this->assertTrue($cron->isDue('* * * * *', $time));
        $this->assertTrue($cron->isDue('15 10 * * *', $time));
        $this->assertTrue($cron->isDue('*/5 * * * *', $time));
        $this->assertTrue($cron->isDue('10-20 * 31 10 6', $time));
        $this->assertFalse($cron->isDue('16 10 * * *', $time));
        $this->assertFalse($cron->isDue('*/10 * * * *', $time));
        $this->assertFalse($cron->isDue('* * * * 1', $time));
        $this->assertFalse($cron->isDue('* * *', $time));
    }

    /**
     * Долгое первое задание не сдвигает проверку остальных: все расписания сверяются с минутой запуска
     */
    public function testSlowJobDoesNotShiftScheduleOfNextJobs(): void
    {
        ExtensionConfig::reset([]);
        $jobs = [
            'first' => new CronJob(name: 'First', handler: [self::class, 'noop'], timing: '15 10 * * *'),
            'second' => new CronJob(name: 'Second', handler: [self::class, 'noop'], timing: '15 10 * * *'),
        ];
        (new \ReflectionProperty(CronRegistry::class, 'jobs'))->setValue(null, $jobs);

        // таблица cron: записи обоих заданий, обе активны
        $rows = [
            ['id' => 1, 'job' => 'first', 'timing' => '15 10 * * *', 'active' => 1],
            ['id' => 2, 'job' => 'second', 'timing' => '15 10 * * *', 'active' => 1],
        ];
        $db = $this->mockDb();
        $pending = [];
        $db->method('query')->willReturnCallback(function (string $q) use (&$pending, $rows) {
            $pending = str_starts_with(strtolower(ltrim($q)), 'select') && str_contains($q, 'cron') ? $rows : [];
            return true;
        });
        $db->method('fetch')->willReturnCallback(function () use (&$pending) {
            return array_shift($pending) ?? false;
        });

        $cron = new class () extends Cron {
            /** @var list<string> */
            public array $ran = [];
            private int $time = 0;

            // каждое обращение к часам - на минуту позже, как после долгого задания
            protected function getTime(): int
            {
                $this->time = $this->time ?: mktime(10, 15, 0, 10, 31, 2026);
                $time = $this->time;
                $this->time += 60;
                return $time;
            }

            protected function runAndLog(string $key, CronJob $job): void
            {
                $this->getTime();
                $this->ran[] = $key;
            }
        };
        $cron->execute();

        $this->assertSame(['first', 'second'], $cron->ran);
    }

    public static function noop(): void
    {
    }
}
