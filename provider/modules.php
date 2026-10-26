<?php

/*
 * Modules available for placing on the site pages (module_item).
 * Settings are static methods of the module: extensionTitle(), modulePostexec()
 * The modules of the admin panel layout are declared in \Simflex\Admin\AdminModules
 */

return [
    \Simflex\Extensions\Code\Code::class,
    \Simflex\Extensions\Block\Block::class,
];
