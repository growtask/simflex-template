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

// get command data
$cmd = array_map(fn(string $part) => strtolower($part), explode('/', $request->arg($isHelp ? 1 : 0)));
if (count($cmd) != 2) {
    Log::error('Usage: ./sf provider/method [args]');
    exit;
}

// create cli bootstrapper
$cli = new Cli($cmd);

// check if provider exists
if (!$cli->hasProvider()) {
    Log::error('Command provider {provider} not found. Available providers:', ['provider' => $cli->provider]);
    $cli->printProviders();
    exit;
}

// try loading the class
if (!$cli->tryLoadClass()) {
    Log::emergency('Provider {provider}\'s class does not exist', ['provider' => $cli->provider]);
    exit;
}

// try loading method
if (!$cli->tryLoadMethod()) {
    Log::error(
        'Method {method} not found in {provider}. Available methods:',
        ['method' => $cli->command, 'provider' => $cli->provider]
    );

    $cli->printMethods();
    exit;
}

// check if the user wants to get help
if ($isHelp) {
    $cli->printHelp();
    exit;
}

// execute the command
$cli->execute(array_slice($request->arg(), 1));