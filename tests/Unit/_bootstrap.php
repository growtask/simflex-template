<?php

// константы ядра и сайта (SF_ROOT_PATH и другие), как их определяют точки входа сайта
$root = dirname(__DIR__, 2);
require_once $root . '/vendor/growtask/simflex/src/constants.php';
require_once $root . '/constants.php';

// сервисы, которые тестируемый код берёт из контейнера: каждый тест получает их независимо от порядка
\Simflex\Core\Container::set('events', new \Simflex\Core\EventManager());
\Simflex\Core\Container::set('factory', new \Simflex\Core\Factory());
