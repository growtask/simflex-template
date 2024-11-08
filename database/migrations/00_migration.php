<?php

use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('migration', function (Schema\Table $c) {
            $c->id('id');
            $c->string('file');
        });

        $s->createTable('seeder', function (Schema\Table $c) {
            $c->id('id');
            $c->string('file');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('seeder');
        $s->dropTable('migration');
    }
};