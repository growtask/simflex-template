<?php

/*
 * Шаблоны страниц: ключ => \Simflex\Extensions\Content\ContentTemplate. Страница ссылается на шаблон по ключу.
 * file: относительно Extensions/Content/tpl. params: дополнительные поля формы страницы, сгруппированные в блоки;
 * их значения — в $content->params по имени поля.
 * Поля, объявленные Layout-компонентами страницы, можно сгенерировать: ./sf layout/generate /page/path
 */

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Extensions\Content\ContentTemplate;
use Simflex\Extensions\Content\ContentTemplateBlock;

return [
    'index' => new ContentTemplate(
        name: 'Главная',
        file: 'index.tpl',
        params: [
            new ContentTemplateBlock('SEO', [
                new FieldDefinition(name: 'seo_title_1', class: FieldString::class, label: 'SEO заголовок 1'),
                new FieldDefinition(
                    name: 'seo_text_1',
                    class: FieldText::class,
                    label: 'SEO текст 1',
                    editorFull: true,
                ),
                new FieldDefinition(name: 'seo_title_2', class: FieldString::class, label: 'SEO заголовок 2'),
                new FieldDefinition(
                    name: 'seo_text_2',
                    class: FieldText::class,
                    label: 'SEO текст 2',
                    editorFull: true,
                ),
            ]),
            new ContentTemplateBlock('Мета', [
                new FieldDefinition(name: 'meta_title', class: FieldString::class, label: 'Заголовок'),
                new FieldDefinition(name: 'meta_kw', class: FieldText::class, label: 'Ключевые слова'),
                new FieldDefinition(name: 'meta_de', class: FieldText::class, label: 'Описание'),
            ], pos: 'right'),
        ],
    ),
];
