<?php

/*
 * Fill the required services
 * - 'prod' => will load either way
 * - 'dev' => will load only if devMode is enabled
 */

return [
    'prod' => [
        \Simflex\Core\Html\HtmlRequest::class,
        \Simflex\Core\Html\HtmlResponse::class,
        \Simflex\Core\Factory::class,
        \Simflex\Core\EventManager::class,
    ],
    'dev' => [
        \Simflex\Core\Wrapper\DebugBar::class,
        \Simflex\Core\Wrapper\Whoops::class,
    ]
];