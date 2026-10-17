<?php

return new class () implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        \Simflex\Extensions\Content\Model\ModelContent::bulkInsert([
            [
                'pid' => null,
                'active' => 1,
                'date' => '2023-09-01',
                'title' => 'Главная',
                'alias' => '/',
                'path' => '/',
                'short' => '<p>test</p>',
                'text' => '',
                'params' => '',
                'file' => '',
                'photo' => '',
                'template' => 'index', // provider/templates.php
                'npp' => 10,
            ],
        ]);
    }
};
