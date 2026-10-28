<?php

/*
 * Единственная точка входа для веб-сервера. Корень веб-сервера - эта директория (public/):
 * здесь лежат только index.php и статика, весь код - уровнем выше, в корне сайта.
 * Сайт, админка (адрес /admin задаёт ADMIN_PATH в .env) и API файлового менеджера - \Simflex\Core\Kernel::runHttp()
 */

require_once __DIR__ . '/../vendor/autoload.php';

new \Simflex\Core\Kernel(dirname(__DIR__))->runHttp();
