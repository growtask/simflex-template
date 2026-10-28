<?php

use Simflex\DB\Schema;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_privilege_personal', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('user_id')->foreignKey('user');
            $c->string('privilege', 64);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_privilege_personal');
    }
};
