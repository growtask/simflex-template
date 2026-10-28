<?php

// константы сайта (SF_ROOT_PATH и другие), как их определяют точки входа сайта; константы ядра подключает
// автозагрузчик composer
require_once dirname(__DIR__, 2) . '/constants.php';

// окружение задаёт точка входа (Application::init()): код в unit-тестах выполняется как на сайте
define('SF_LOCATION', SF_LOCATION_SITE);

// сервисы, которые тестируемый код берёт из контейнера: каждый тест получает их независимо от порядка
\Simflex\Core\Container::set('events', new \Simflex\Core\EventManager());
\Simflex\Core\Container::set('factory', new \Simflex\Core\DI\Factory());
