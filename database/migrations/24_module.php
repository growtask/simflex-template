<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('module', function (Schema\Table $c) {
            $c->id('module_id');
            $c->string('class');
            $c->string('name');
            $c->enum('type', ['site', 'admin'])->setDefault('site');
            $c->integer('postexec')->setDefault(0);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('module');
    }
};
