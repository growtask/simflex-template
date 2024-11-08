<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('cron', function (Schema\Table $c) {
            $c->id('id');
            $c->boolean('active')->setDefault(1);
            $c->string('timing', 30);
            $c->string('name');
            $c->integer('ext_id');
            $c->integer('module_id');
            $c->string('plugin_name', 50);
            $c->string('action');
            $c->string('cparams', 1023);
            $c->string('class_fqn');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('cron');
    }
};