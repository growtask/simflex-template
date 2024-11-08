<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('struct_table', function (Schema\Table $c) {
            $c->id('table_id');
            $c->string('name');
            $c->string('order_by');
            $c->integer('order_desc');
            $c->integer('priv_add');
            $c->integer('priv_edit');
            $c->integer('priv_delete');
            $c->string('class');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_table');
    }
};