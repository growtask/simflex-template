<?php

require_once 'vendor/autoload.php';

$mgr = new \Simflex\FileManager\Manager(dirname(__FILE__) . '/uf', function () {
    return !!\Simflex\Core\Container::getUser();
});

$mgr->handleRequest();