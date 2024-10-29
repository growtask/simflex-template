<?php

return new class implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        $contentComponent = \Simflex\Core\Models\Component::byClass(\App\Extensions\Content\Content::class);

        \Simflex\Core\Models\Menu::bulkInsert([
            [
                'menu_pid' => null,
                'component_id' => $contentComponent->component_id,
                'active' => true,
                'hidden' => false,
                'npp' => 1,
                'name' => 'Главная',
                'link' => '/'
            ]
        ]);
    }
};