<?php

use Simflex\Core\Models\User;
use Simflex\Core\Models\UserRole;

return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
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
                'code' => ''
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
                'code' => ''
            ]
        ]);
    }
};