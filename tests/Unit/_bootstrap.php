<?php

// the constants of the core and the site (SF_ROOT_PATH and others), as the site's entry points define them
$root = dirname(__DIR__, 2);
require_once $root . '/vendor/growtask/simflex/src/constants.php';
require_once $root . '/constants.php';

// the services the code under test takes from the container: every test gets them regardless of the order
\Simflex\Core\Container::set('events', new \Simflex\Core\EventManager());
\Simflex\Core\Container::set('factory', new \Simflex\Core\Factory());
