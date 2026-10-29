<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->string('component');
            $c->boolean('active')->setDefault(0);
            $c->boolean('hidden')->setDefault(0);
            // Где выводится: основное меню (шапка) или подвал (корневые пункты - колонки, вложенные - их ссылки)
            $c->enum('place', ['main', 'footer'])->setDefault('main')->comment('Основное меню;;Подвал');
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
        });

        $s->seed(function () {
            \Simflex\Core\Models\Menu::bulkInsert([
                [
                    'menu_pid' => null,
                    'component' => \App\Extensions\Content\Content::class,
                    'active' => true,
                    'hidden' => false,
                    'place' => 'main',
                    'npp' => 1,
                    'name' => 'Главная',
                    'link' => '/',
                ],
            ]);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('menu');
    }
};
