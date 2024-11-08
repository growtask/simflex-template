<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('module_item', function (Schema\Table $c) {
            $c->id('item_id');
            $c->integer('module_id')->foreignKey('module');
            $c->integer('menu_id');
            $c->string('posname')->index();
            $c->boolean('active')->setDefault(0);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->longText('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('module_item');
    }
};