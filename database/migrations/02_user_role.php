<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_role', function (Schema\Table $c) {
            $c->id('role_id');
            $c->string('privilege', 64);
            $c->boolean('active');
            $c->string('alias');
            $c->string('name');
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_role');
    }
};
