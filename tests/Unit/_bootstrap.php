<?php

// пути сайта и окружение, как их задаёт ядро при загрузке (Core, Core::init()): код в unit-тестах выполняется
// как на сайте
\Simflex\Core\System\Container::set('paths', new \Simflex\Core\System\Paths(dirname(__DIR__, 2)));
\Simflex\Core\System\Container::set('location', \Simflex\Core\System\Location::Site);

// сервисы, которые тестируемый код берёт из контейнера: каждый тест получает их независимо от порядка
\Simflex\Core\System\Container::set('events', new \Simflex\Core\System\EventManager());
\Simflex\Core\System\Container::set('factory', new \Simflex\Core\System\DI\Factory());
