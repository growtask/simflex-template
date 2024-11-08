<?php

/*
 * Fill the required services
 * - 'all' => will load for all environments
 * - 'web' => will load only for site/admin environments
 * - 'api' => will load only for api environment
 * - 'cli' => will load only for cli/cron environments
 * - 'dev' => will load only if devMode is enabled
 */

return [
    'all' => [
        \Simflex\Core\Factory::class,
        \Simflex\Core\EventManager::class,
    ],
    'web' => [
        \Simflex\Core\Html\HtmlRequest::class,
        \Simflex\Core\Html\HtmlResponse::class,
    ],
    'api' => [
        \Simflex\Core\Api\JsonRequest::class,
        \Simflex\Core\Api\JsonResponse::class,
    ],
    'cli' => [
        \Simflex\Core\Console\CliRequest::class,
    ],
    'dev' => [
        \Simflex\Core\Wrapper\DebugBar::class,
        \Simflex\Core\Wrapper\Whoops::class,
    ]
];