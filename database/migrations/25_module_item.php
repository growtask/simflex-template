<?php

use Simflex\Core\DB\Schema;
use Simflex\Core\Models\ModuleItem;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('module_item', function (Schema\Table $c) {
            $c->id('item_id');
            $c->string('module');
            $c->integer('menu_id');
            $c->string('posname')->index();
            $c->boolean('active')->setDefault(0);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->longText('params');
        });

        $s->seed(function () {
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
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('module_item');
    }
};
