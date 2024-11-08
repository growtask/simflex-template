<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('component_param', function (Schema\Table $c) {
            $c->id('cp_id');
            $c->integer('component_id')->foreignKey('component');
            $c->integer('param_pid');
            $c->string('position', 50);
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name', 50);
            $c->string('label', 50);
            $c->integer('npp')->setDefault(0);
            $c->string('help')->setDefault('');
            $c->longText('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('component_param');
    }
};