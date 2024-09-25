<?php

require_once __DIR__ . '/../Core/Simflex.php';

$sf = new \App\Core\Simflex();

const SF_LOCATION = SF_LOCATION_ADMIN;
$sf->init();
$sf->execute(false);
