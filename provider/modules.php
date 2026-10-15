<?php

/*
 * Modules available for placing on the pages.
 * Settings are static methods of the module: extensionTitle(), moduleType() (ModuleType::Site|Admin), modulePostexec()
 */

return [
    \Simflex\Admin\Modules\Menu\Menu::class,
    \Simflex\Admin\Modules\Breadcrumbs\Breadcrumbs::class,
    \Simflex\Extensions\Code\Code::class,
    \Simflex\Extensions\Block\Block::class,
];
