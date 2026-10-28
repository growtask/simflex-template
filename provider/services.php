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
        \Simflex\Core\System\DI\Factory::class,
        \Simflex\Core\System\EventManager::class,
    ],
    'web' => [
        \Simflex\Core\Http\Html\HtmlRequest::class,
        \Simflex\Core\Http\Html\HtmlResponse::class,
    ],
    'api' => [
        \Simflex\Core\Http\Api\JsonRequest::class,
        \Simflex\Core\Http\Api\JsonResponse::class,
    ],
    'cli' => [
        \Simflex\Core\Console\CliRequest::class,
    ],
    'dev' => [
        \Simflex\Core\Debug\DebugBar::class,
        \Simflex\Core\Debug\Whoops::class,
    ],
];
