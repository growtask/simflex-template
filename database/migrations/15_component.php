<?php

use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('component', function (Schema\Table $c) {
            $c->id('component_id');
            $c->string('class');
            $c->string('name');
            $c->longText('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('component');
    }
};