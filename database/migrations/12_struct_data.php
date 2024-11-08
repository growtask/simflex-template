<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('struct_data', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('npp')->setDefault(0);
            $c->integer('table_id')->foreignKey('struct_table');
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name', 50)->index();
            $c->string('label', 50);
            $c->string('help')->setDefault('');
            $c->string('placeholder')->setDefault('');
            $c->text('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_data');
    }
};