<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('user_role', function (Schema\Table $c) {
            $c->id('role_id');
            $c->integer('priv_id')->foreignKey('user_priv');
            $c->boolean('active');
            $c->integer('npp');
            $c->string('alias');
            $c->string('name');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_role');
    }
};