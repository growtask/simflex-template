<?php

/*
 * Нужные сервисы
 * - 'all' => загружаются во всех окружениях
 * - 'web' => только в окружениях сайта и админки
 * - 'api' => только в окружении api
 * - 'cli' => только в окружениях cli/cron
 * - 'dev' => только при включённом devMode
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
    ],
];
