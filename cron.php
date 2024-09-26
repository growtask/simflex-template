<?php

require_once 'Core/Simflex.php';

$sf = new \App\Core\Simflex();
const SF_LOCATION = SF_LOCATION_CLI;
$sf->init();

try {
    $cron = new \Simflex\Core\Cron\CronBootstrap($_REQUEST['cron_id'] ?? null);
    $cron->execute();
} catch (Exception $e) {
    \Simflex\Core\Log::critical('Cron error: {error}', ['error' => $e->getMessage()]);
}