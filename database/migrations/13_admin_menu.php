<?php

use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('admin_menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->integer('priv_id')->foreignKey('user_priv');
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
            $c->string('model');
            $c->string('icon');
            $c->boolean('hidden')->setDefault(0);
        });
        // todo: add keys post-factum
    }

    public function down(Schema $s)
    {
        $s->dropTable('admin_menu');
    }
};