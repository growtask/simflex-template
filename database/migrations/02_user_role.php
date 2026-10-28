<?php

use App\Core\Privilege;
use Simflex\DB\Schema;
use Simflex\Models\UserRole;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_role', function (Schema\Table $c) {
            $c->id('role_id');
            $c->string('privilege', 64);
            $c->boolean('active');
            $c->string('alias');
            $c->string('name');
        });

        // привилегии, которые дают роли, — в 03_user_role_privilege
        $s->seed(function () {
            UserRole::bulkInsert([
                ['privilege' => Privilege::DEV, 'active' => true, 'name' => 'Разработчик', 'alias' => 'dev'],
                ['privilege' => Privilege::ADMIN, 'active' => true, 'name' => 'Администратор', 'alias' => 'admin'],
                ['privilege' => Privilege::ADMIN, 'active' => true, 'name' => 'Пользователь', 'alias' => 'user'],
                ['privilege' => Privilege::ADMIN, 'active' => true, 'name' => 'Менеджер', 'alias' => 'manager'],
            ]);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_role');
    }
};
