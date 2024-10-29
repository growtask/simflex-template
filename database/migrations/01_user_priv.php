<?php

use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('user_priv', function (Schema\Table $c) {
            $c->id('priv_id');
            $c->boolean('active');
            $c->integer('npp');
            $c->string('name');
            $c->string('comment');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_priv');
    }
};