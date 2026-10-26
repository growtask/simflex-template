<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_privilege', function (Schema\Table $c) {
            // необязательная: активность, переключённая в админке, сами привилегии объявлены в коде (Privilege)
            $c->id('privilege_id');
            $c->string('name', 64)->unique();
            $c->boolean('active')->setDefault(1);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_privilege');
    }
};
