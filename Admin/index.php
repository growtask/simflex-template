<?php

require_once __DIR__ . '/../Core/Init.php';

Init::loadConstants();
define('SF_LOCATION', SF_LOCATION_ADMIN);

Init::_();

\Simflex\Admin\Core::execute();
