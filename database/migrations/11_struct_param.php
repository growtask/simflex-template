<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('struct_param', function (Schema\Table $c) {
            $c->id('param_id');
            $c->integer('param_pid');
            $c->integer('table_id')->foreignKey('struct_table');
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('pos', 50);
            $c->string('name', 50);
            $c->string('label', 50);
            $c->text('default_value');
            $c->text('params');
            $c->integer('npp');
            $c->string('group_name');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_param');
    }
};