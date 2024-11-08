<?php
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->integer('component_id')->foreignKey('component');
            $c->boolean('active')->setDefault(0);
            $c->boolean('hidden')->setDefault(0);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('menu');
    }
};