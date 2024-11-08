<?php

use Simflex\Core\Models\UserPriv;
use Simflex\Core\Models\UserRole;

return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
        $devPriv = UserPriv::byName('dev');
        $adminPriv = UserPriv::byName('admin');
        $simplexAdminPriv = UserPriv::byName('simplex_admin');
        $debugPriv = UserPriv::byName('debug');
        $managerPriv = UserPriv::byName('manager');

        $devRole = UserRole::insertStatic([
            'priv_id' => $devPriv->priv_id,
            'active' => true,
            'npp' => 1,
            'name' => 'Разработчик',
            'alias' => 'dev',
        ]);

        $devRole->addPriv($devPriv);
        $devRole->addPriv($adminPriv);
        $devRole->addPriv($simplexAdminPriv);
        $devRole->addPriv($debugPriv);

        $adminRole = UserRole::insertStatic([
            'priv_id' => $adminPriv->priv_id,
            'active' => true,
            'npp' => 2,
            'name' => 'Администратор',
            'alias' => 'admin',
        ]);

        $adminRole->addPriv($adminPriv);
        $adminRole->addPriv($simplexAdminPriv);
        $adminRole->addPriv($managerPriv);

        UserRole::insertStatic([
            'priv_id' => $adminPriv->priv_id,
            'active' => true,
            'npp' => 3,
            'name' => 'Пользователь',
            'alias' => 'user',
        ]);

        $managerRole = UserRole::insertStatic([
            'priv_id' => $adminPriv->priv_id,
            'active' => true,
            'npp' => 4,
            'name' => 'Менеджер',
            'alias' => 'manager',
        ]);

        $managerRole->addPriv($simplexAdminPriv);
        $managerRole->addPriv($managerPriv);
    }
};