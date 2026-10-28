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
        \Simflex\Core\DI\Factory::class,
        \Simflex\Core\EventManager::class,
    ],
    'web' => [
        \Simflex\Http\Html\HtmlRequest::class,
        \Simflex\Http\Html\HtmlResponse::class,
    ],
    'api' => [
        \Simflex\Http\Api\JsonRequest::class,
        \Simflex\Http\Api\JsonResponse::class,
    ],
    'cli' => [
        \Simflex\Console\CliRequest::class,
    ],
    'dev' => [
        \Simflex\Debug\DebugBar::class,
        \Simflex\Debug\Whoops::class,
    ],
];
