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
                    'params' => serialize([
                        'slides' => json_encode(['s' => [], 'v' => [
                            ['title' => 'Добро пожаловать', 'text' => 'Сайт на Simflex CMS: быстрый старт и удобное управление.', 'image' => '/assets/default/images/banner/main.webp', 'buttonText' => 'Подробнее', 'buttonLink' => '/'],
                            ['title' => 'Всё под рукой', 'text' => 'Редактируйте слайды и блоки главной страницы в админке.', 'image' => '/assets/default/images/banner/main-2.webp', 'buttonText' => 'Связаться', 'buttonLink' => '/'],
                            ['title' => 'Готово к росту', 'text' => 'Подключайте расширения: каталог, магазин, блог.', 'image' => '/assets/default/images/banner/main-3.webp', 'buttonText' => 'Подробнее', 'buttonLink' => '/'],
                        ]], JSON_UNESCAPED_UNICODE),
                        'features_title' => 'Наши преимущества',
                        'benefits' => json_encode(['s' => [], 'v' => [
                            ['title' => 'Быстро', 'text' => 'Запуск сайта за считанные дни.', 'icon' => 'check'],
                            ['title' => 'Надёжно', 'text' => 'Проверенное ядро и тесты.', 'icon' => 'check'],
                            ['title' => 'Удобно', 'text' => 'Понятная админка для редактора.', 'icon' => 'check'],
                            ['title' => 'Гибко', 'text' => 'Расширения под задачи бизнеса.', 'icon' => 'check'],
                        ]], JSON_UNESCAPED_UNICODE),
                        'rform_title' => 'Связаться с нами',
                        'rform_desc' => 'Оставьте заявку, и мы свяжемся с вами в ближайшее время.',
                        'rform_image' => '/assets/default/images/banner/form.webp',
                    ]),
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
