<?php

use Simflex\Core\Models\ModuleItem;

return new class () implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        ModuleItem::bulkInsert([
            [
                'module' => \Simflex\Admin\Modules\Menu\Menu::class,
                'menu_id' => null,
                'posname' => 'menu',
                'active' => 1,
                'npp' => 1,
                'name' => 'Admin - Меню',
                'params' => '',
            ],
            [
                'module' => \Simflex\Admin\Modules\Breadcrumbs\Breadcrumbs::class,
                'menu_id' => null,
                'posname' => 'breadcrumbs',
                'active' => 1,
                'npp' => 2,
                'name' => 'Admin - Хлебные крошки',
                'params' => '',
            ],
        ]);
    }
};
