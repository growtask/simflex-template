<?php

/*
 * Cron jobs: key => \Simflex\Core\Cron\CronJob.
 * timing and active are the defaults: the admin panel (Управление → Cron) keeps the edited values in the DB.
 * Run a job right now: php cron.php --job=<key>
 */

use Simflex\Core\Cron\CronJob;

return [
    'cron_log_clean' => new CronJob(
        name: 'Очистка журнала cron',
        handler: [\Simflex\Core\Models\CronLog::class, 'deleteOld'],
        timing: '30 0 * * *',
    ),
];
