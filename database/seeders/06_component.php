<?php
return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
        \Simflex\Core\Models\Component::bulkInsert([
            [
                'class' => \App\Extensions\Content\Content::class,
                'name' => 'Материалы',
                'params' => ''
            ]
        ]);
    }
};