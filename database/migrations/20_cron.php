<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('cron', function (Schema\Table $c) {
            $c->id('id');
            $c->string('job')->unique();
            $c->boolean('active')->setDefault(1);
            $c->string('timing', 30);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('cron');
    }
};
