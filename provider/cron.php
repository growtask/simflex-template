<?php

/*
 * Задачи cron: ключ => \Simflex\Cron\CronJob.
 * timing и active — значения по умолчанию: админка (Управление → Cron) хранит изменённые значения в БД.
 * Запустить задачу сейчас: php cron.php --job=<key>
 */

use Simflex\Cron\CronJob;

return [
    'cron_log_clean' => new CronJob(
        name: 'Очистка журнала cron',
        handler: [\Simflex\Models\CronLog::class, 'deleteOld'],
        timing: '30 0 * * *',
    ),
];
