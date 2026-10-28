<?php

use Simflex\DB\Schema;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('cron_log', function (Schema\Table $c) {
            $c->id('id');
            $c->string('job', 100)->index();
            $c->dateTime('datetime');
            $c->string('result', 2047);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('cron_log');
    }
};
