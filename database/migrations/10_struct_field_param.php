<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('struct_field_param', function (Schema\Table $c) {
            $c->id('fp_id');
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name');
            $c->string('label');
            $c->integer('type_id')->foreignKey('struct_field', 'field_id');
            $c->string('help');
            $c->text('default_value');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_field_param');
    }

    public function seed()
    {

    }
};