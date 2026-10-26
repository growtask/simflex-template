<?php

use App\Core\Simflex;
use Simflex\Core\Console\Cli;
use Simflex\Core\Console\CliRequest;
use Simflex\Core\Container;
use Simflex\Core\Log;

require_once 'Core/Simflex.php';

$sf = new Simflex();

const SF_LOCATION = SF_LOCATION_CLI;
$sf->init();

/** @var CliRequest $request */
$request = Container::getRequest();

if (!$request->arg()) {
    Log::error('Usage: ./sf {help} provider/method [args]');
    exit;
}

$isHelp = strtolower($request->arg(0)) == 'help';

// данные команды
$cmd = array_map(fn(string $part) => strtolower($part), explode('/', $request->arg($isHelp ? 1 : 0)));
if (count($cmd) != 2) {
    Log::error('Usage: ./sf provider/method [args]');
    exit;
}

// загрузчик CLI
$cli = new Cli($cmd);

// проверяем, есть ли провайдер
if (!$cli->hasProvider()) {
    Log::error('Command provider {provider} not found. Available providers:', ['provider' => $cli->provider]);
    $cli->printProviders();
    exit;
}

// пробуем загрузить класс
if (!$cli->tryLoadClass()) {
    Log::emergency('Provider {provider}\'s class does not exist', ['provider' => $cli->provider]);
    exit;
}

// пробуем загрузить метод
if (!$cli->tryLoadMethod()) {
    Log::error(
        'Method {method} not found in {provider}. Available methods:',
        ['method' => $cli->command, 'provider' => $cli->provider]
    );

    $cli->printMethods();
    exit;
}

// пользователь запросил справку
if ($isHelp) {
    $cli->printHelp();
    exit;
}

// выполняем команду
$cli->execute(array_slice($request->arg(), 1));
