<?php

use App\Core\Privilege;
use Simflex\Core\Models\UserRole;

return new class () implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        $devRole = UserRole::insertStatic([
            'privilege' => Privilege::DEV,
            'active' => true,
            'name' => 'Разработчик',
            'alias' => 'dev',
        ]);

        $devRole->addPrivilege(Privilege::DEV);
        $devRole->addPrivilege(Privilege::ADMIN);

        $adminRole = UserRole::insertStatic([
            'privilege' => Privilege::ADMIN,
            'active' => true,
            'name' => 'Администратор',
            'alias' => 'admin',
        ]);

        $adminRole->addPrivilege(Privilege::ADMIN);
        $adminRole->addPrivilege(Privilege::MANAGER);

        UserRole::insertStatic([
            'privilege' => Privilege::ADMIN,
            'active' => true,
            'name' => 'Пользователь',
            'alias' => 'user',
        ]);

        $managerRole = UserRole::insertStatic([
            'privilege' => Privilege::ADMIN,
            'active' => true,
            'name' => 'Менеджер',
            'alias' => 'manager',
        ]);

        $managerRole->addPrivilege(Privilege::ADMIN);
        $managerRole->addPrivilege(Privilege::MANAGER);
    }
};
