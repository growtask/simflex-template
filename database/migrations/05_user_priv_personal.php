<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('user_priv_personal', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('user_id')->foreignKey('user');
            $c->integer('priv_id')->foreignKey('user_priv');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_priv_personal');
    }
};