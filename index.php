<?php

require_once 'Core/Simflex.php';

$sf = new \App\Core\Simflex();

const SF_LOCATION = SF_LOCATION_SITE;
$sf->init();
$data = $sf->execute(true);
echo $sf->prepareOutput($data);