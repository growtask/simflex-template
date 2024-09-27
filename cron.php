<?php

use App\Core\Simflex;
use Simflex\Core\Console\CliRequest;
use Simflex\Core\Container;
use Simflex\Core\Cron\Cron;
use Simflex\Core\Log;

require_once 'Core/Simflex.php';

$sf = new Simflex();
const SF_LOCATION = SF_LOCATION_CLI;
$sf->init();

/** @var CliRequest $request */
$request = Container::getRequest();

try {
    $cron = new Cron($request->arg('cron_id'));
    $cron->execute();
} catch (Exception $e) {
    Log::critical('Cron error: {error}', ['error' => $e->getMessage()]);
}