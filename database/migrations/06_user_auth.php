<?php

use Simflex\Core\DB\Schema;
use Simflex\Core\DB\Schema\Params\ConstraintParams;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_auth', function (Schema\Table $c) {
            $c->id('auth_id');
            $c->integer('user_id');
            $c->addConstraint()->foreignKey('user', ['user_id' => 'user_id'])
                ->onDelete(ConstraintParams::REFERENCE_OPTION_CASCADE); // записи удаляются вместе с пользователем
            $c->string('token');
            $c->addColumn('time_create', 'timestamp')->setDefault('CURRENT_TIMESTAMP');
            $c->addColumn('time_last_login', 'timestamp')->index();
            $c->dateTime('time_expires');
            $c->string('useragent');
            $c->string('ip');

            $c->addIndex()->index(['token', 'time_expires']);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_auth');
    }
};
