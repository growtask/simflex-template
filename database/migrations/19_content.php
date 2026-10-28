<?php

use Simflex\Core\DB\Schema;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('content', function (Schema\Table $c) {
            $c->id('content_id');
            $c->integer('pid');
            $c->boolean('active')->setDefault(0);
            $c->date('date');
            $c->string('title');
            $c->string('alias');
            $c->string('path');
            $c->text('short');
            $c->longText('text');
            $c->longText('params');
            $c->string('file');
            $c->string('photo');
            $c->string('template', 64);
            $c->integer('npp');
        });

        $s->seed(function () {
            \Simflex\Core\Extensions\Content\Models\Content::bulkInsert([
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
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('content');
    }
};
