<?php

use Simflex\DB\Schema;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->string('component');
            $c->boolean('active')->setDefault(0);
            $c->boolean('hidden')->setDefault(0);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
        });

        $s->seed(function () {
            \Simflex\Models\Menu::bulkInsert([
                [
                    'menu_pid' => null,
                    'component' => \App\Extensions\Content\Content::class,
                    'active' => true,
                    'hidden' => false,
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
