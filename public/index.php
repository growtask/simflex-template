<?php

/*
 * Единственная точка входа для веб-сервера. Корень сайта - эта директория (public/):
 * здесь лежат только index.php и статика, весь код - уровнем выше.
 * Адрес админки (/admin) задаётся в конфиге: ADMIN_PATH в .env.
 */

require_once __DIR__ . '/../Core/Simflex.php';

$sf = new \App\Core\Simflex();

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

if ($sf->isAdminPath($path)) {
    define('SF_LOCATION', SF_LOCATION_ADMIN);
    $sf->init();
    if ($path === \Simflex\Admin\Core::url('fm')) {
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
