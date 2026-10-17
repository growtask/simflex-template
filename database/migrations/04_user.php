<?php

use Simflex\Core\DB\Schema;
use Simflex\Core\Models\User;
use Simflex\Core\Models\UserRole;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
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

        $s->seed(function () {
            $devRole = UserRole::byAlias('dev');
            $adminRole = UserRole::byAlias('admin');

            User::bulkInsert([
                [
                    'role_id' => $devRole->role_id,
                    'active' => true,
                    'login' => 'dev',
                    'password' => '827ccb0eea8a706c4c34a16891f84e7b', // 12345
                    'hash' => '',
                    'hash_admin' => '',
                    'email' => 'dev@simflex',
                    'name' => 'Developer',
                    'code' => '',
                ],
                [
                    'role_id' => $adminRole->role_id,
                    'active' => true,
                    'login' => 'admin',
                    'password' => '827ccb0eea8a706c4c34a16891f84e7b', // 12345
                    'hash' => '',
                    'hash_admin' => '',
                    'email' => 'admin@simflex',
                    'name' => 'Administrator',
                    'code' => '',
                ],
            ]);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user');
    }
};
