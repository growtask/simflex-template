<?php

use Simflex\Core\Models\Module;
use Simflex\Core\Models\ModuleItem;

return new class implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        $adminMenu = Module::insertStatic([
            'class' => 'Menu',
            'name' => 'Admin - Меню',
            'type' => 'admin',
            'postexec' => 0,
        ]);

        ModuleItem::bulkInsert([
            [
                'module_id' => $adminMenu->module_id,
                'menu_id' => null,
                'posname' => 'menu',
                'active' => 1,
                'npp' => 1,
                'name' => 'Admin - Меню',
                'params' => ''
            ],
        ]);

        $adminBc = Module::insertStatic([
            'class' => 'Breadcrumbs',
            'name' => 'Admin - Хлебные крошки',
            'type' => 'admin',
            'postexec' => 0,
        ]);

        ModuleItem::bulkInsert([
            [
                'module_id' => 2,
                'menu_id' => null,
                'posname' => 'breadcrumbs',
                'active' => 1,
                'npp' => 2,
                'name' => 'Admin - Хлебные крошки',
                'params' => ''
            ],
        ]);
    }
};