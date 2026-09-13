<?php

return new class () implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        \Simflex\Core\Models\Menu::bulkInsert([
            [
                'menu_pid' => null,
                'component' => \App\Extensions\Content\Content::class,
                'active' => true,
                'hidden' => false,
                'npp' => 1,
                'name' => 'Главная',
                'link' => '/',
            ],
        ]);
    }
};
