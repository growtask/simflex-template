<?php

use Simflex\DB\Schema;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('log', function (Schema\Table $c) {
            $c->id('log_id');
            $c->timestamp('datetime')->setDefault('CURRENT_TIMESTAMP');
            $c->enum('action', ['login_attempt', 'login_success']);
            $c->string('ip');
            $c->string('browser');
            $c->string('data', 1000);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('log');
    }
};
