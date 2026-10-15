<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('cron', function (Schema\Table $c) {
            $c->id('id');
            $c->boolean('active')->setDefault(1);
            $c->string('timing', 30);
            $c->string('name');
            $c->string('plugin_name', 50);
            $c->string('action');
            $c->string('cparams', 1023);
            $c->string('class_fqn');
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('cron');
    }
};
