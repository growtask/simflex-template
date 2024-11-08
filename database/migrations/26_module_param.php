<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('module_param', function (Schema\Table $c) {
            $c->id('mp_id');
            $c->integer('module_id')->foreignKey('module');
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
        $s->dropTable('module_param');
    }
};