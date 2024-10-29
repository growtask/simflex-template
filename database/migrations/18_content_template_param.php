<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('content_template_param', function (Schema\Table $c) {
            $c->id('ctp_id');
            $c->integer('template_id')->foreignKey('content_template');
            $c->integer('param_pid');
            $c->string('position', 50);
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name', 50);
            $c->string('label', 50);
            $c->integer('npp')->setDefault(0);
            $c->string('help')->setDefault('');
            $c->longText('params');
            $c->string('default_value');
            $c->string('group_name');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('content_template_param');
    }
};