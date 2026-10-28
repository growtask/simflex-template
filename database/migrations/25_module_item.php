<?php

use Simflex\DB\Schema;

return new class () implements \Simflex\DB\Migration {
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
    }

    public function down(Schema $s): void
    {
        $s->dropTable('module_item');
    }
};
