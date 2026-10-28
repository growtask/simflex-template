<?php

use Simflex\DB\Schema;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('migration', function (Schema\Table $c) {
            $c->id('id');
            $c->string('file');
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('migration');
    }
};
