<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
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

    public function down(Schema $s)
    {
        $s->dropTable('log');
    }
};