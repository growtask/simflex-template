<?php

use App\Core\Privilege;
use Simflex\DB\Schema;
use Simflex\Models\UserRole;

return new class () implements \Simflex\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('user_role_privilege', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('role_id')->foreignKey('user_role');
            $c->string('privilege', 64);
        });

        $s->seed(function () {
            $privileges = [
                'dev' => [Privilege::DEV, Privilege::ADMIN],
                'admin' => [Privilege::ADMIN, Privilege::MANAGER],
                'manager' => [Privilege::ADMIN, Privilege::MANAGER],
            ];
            foreach ($privileges as $alias => $names) {
                $role = UserRole::byAlias($alias);
                foreach ($names as $name) {
                    $role->addPrivilege($name);
                }
            }
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('user_role_privilege');
    }
};
