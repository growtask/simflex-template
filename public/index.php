<?php

/*
 * Единственная точка входа для веб-сервера. Корень сайта - эта директория (public/):
 * здесь лежат только index.php и статика, весь код - уровнем выше.
 */

require_once __DIR__ . '/../Core/Simflex.php';

$sf = new \App\Core\Simflex();

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if (preg_match('~^/admin(/|$)~', $path)) {
    define('SF_LOCATION', SF_LOCATION_ADMIN);
    $sf->init();
    if ($path === '/admin/fm') {
        // API файлового менеджера админки
        $sf->executeFileManager();
    } else {
        $sf->execute(false);
    }
} else {
    define('SF_LOCATION', SF_LOCATION_SITE);
    $sf->init();
    $data = $sf->execute(true);
    echo $sf->prepareOutput($data);
}
