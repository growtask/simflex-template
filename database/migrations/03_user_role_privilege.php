<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_role_privilege', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('role_id')->foreignKey('user_role');
            $c->string('privilege', 64);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_role_privilege');
    }
};
