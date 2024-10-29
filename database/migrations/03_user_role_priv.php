<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('user_role_priv', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('role_id')->foreignKey('user_role');
            $c->integer('priv_id')->foreignKey('user_priv');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_role_priv');
    }
};