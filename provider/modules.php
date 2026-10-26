<?php

/*
 * Модули, которые можно размещать на страницах сайта (module_item).
 * Настройки — статические методы модуля: extensionTitle(), modulePostexec()
 * Модули макета админки объявлены в \Simflex\Admin\AdminModules
 */

return [
    \Simflex\Extensions\Code\Code::class,
    \Simflex\Extensions\Block\Block::class,
];
