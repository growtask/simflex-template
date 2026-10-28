<?php

/*
 * Единственная точка входа для веб-сервера. Корень сайта - эта директория (public/):
 * здесь лежат только index.php и статика, весь код - уровнем выше.
 * Сайт, админка (адрес /admin задаёт ADMIN_PATH в .env) и API файлового менеджера - \Simflex\Application::runHttp()
 */

require_once __DIR__ . '/../constants.php';
require_once SF_ROOT_PATH . '/vendor/autoload.php';

(new \Simflex\Application())->runHttp();
