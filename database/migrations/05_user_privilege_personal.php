<?php

use Simflex\Core\DB\Schema;
use Simflex\Core\DB\Schema\Params\ConstraintParams;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_privilege_personal', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('user_id');
            $c->addConstraint()->foreignKey('user', ['user_id' => 'user_id'])
                ->onDelete(ConstraintParams::REFERENCE_OPTION_CASCADE); // записи удаляются вместе с пользователем
            $c->string('privilege', 64);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_privilege_personal');
    }
};
