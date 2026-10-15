<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('component', function (Schema\Table $c) {
            $c->id('component_id');
            $c->string('class');
            $c->string('name');
            $c->longText('params');
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('component');
    }
};
