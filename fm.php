<?php

require_once 'Core/Simflex.php';

$sf = new \App\Core\Simflex();
const SF_LOCATION = SF_LOCATION_SITE;
$sf->init();

$mgr = new \Simflex\FileManager\Manager(dirname(__FILE__) . '/uf', function () {
    return !!\Simflex\Core\Container::getUser();
});

$mgr->handleRequest();