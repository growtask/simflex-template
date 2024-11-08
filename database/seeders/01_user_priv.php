<?php

use Simflex\Core\Models\UserPriv;

return new class implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        UserPriv::bulkInsert([
            [
                'active' => true,
                'npp' => 1,
                'name' => 'dev',
                'comment' => 'Привилегия разработчика'
            ],
            [
                'active' => true,
                'npp' => 2,
                'name' => 'admin',
                'comment' => 'Привилегия администратора'
            ],
            [
                'active' => true,
                'npp' => 2,
                'name' => 'simplex_admin',
                'comment' => 'Доступ к административному интерфейсу'
            ],
            [
                'active' => true,
                'npp' => 3,
                'name' => 'debug',
                'comment' => 'Показывать отладчик'
            ],
            [
                'active' => true,
                'npp' => 4,
                'name' => 'manager',
                'comment' => 'Менеджер'
            ],
        ]);
    }
};