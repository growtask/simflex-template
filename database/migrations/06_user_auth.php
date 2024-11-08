<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('user_auth', function (Schema\Table $c) {
            $c->id('auth_id');
            $c->integer('user_id')->foreignKey('user');
            $c->string('token');
            $c->addColumn('time_create', 'timestamp')->setDefault('CURRENT_TIMESTAMP');
            $c->addColumn('time_last_login', 'timestamp')->index();
            $c->dateTime('time_expires');
            $c->string('useragent');
            $c->string('ip');

            $c->addIndex()->index(['token', 'time_expires']);
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_auth');
    }
};