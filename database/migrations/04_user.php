<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('user', function (Schema\Table $c) {
            $c->id('user_id');
            $c->integer('role_id')->foreignKey('user_role')->setDefault(3);
            $c->boolean('active')->setDefault(1);
            $c->string('login')->unique(true);
            $c->string('password');
            $c->string('hash'); // deprecated
            $c->string('hash_admin'); // deprecated
            $c->string('email')->unique(true);
            $c->string('name');
            $c->string('code');
            $c->boolean('in_mailing')->setDefault(1);
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user');
    }
};