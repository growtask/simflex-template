<?php

/*
 * Модули, которые можно размещать на страницах сайта (module_item).
 * Настройки — статические методы модуля: extensionTitle(), modulePostexec()
 * Модули макета админки объявлены в \Simflex\Core\Admin\AdminModules
 */

return [
    \App\Extensions\Menu\Menu::class,
    \Simflex\Core\Extensions\Code\Code::class,
    \Simflex\Core\Extensions\Block\Block::class,
];
