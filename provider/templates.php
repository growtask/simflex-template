<?php

/*
 * Шаблоны страниц: ключ => \Simflex\Core\Extensions\Content\ContentTemplate. Страница ссылается на шаблон по ключу.
 * file: относительно Extensions/Content/tpl. params: дополнительные поля формы страницы, сгруппированные в блоки;
 * их значения — в $content->params по имени поля.
 * Поля, объявленные Layout-компонентами страницы, можно сгенерировать: ./sf layout/generate /page/path
 */

use Simflex\Core\Admin\Fields\FieldString;
use Simflex\Core\Admin\Fields\FieldTable;
use Simflex\Core\Admin\Fields\FieldText;
use Simflex\Core\Admin\Table\FieldDefinition;
use Simflex\Core\Extensions\Content\ContentTemplate;
use Simflex\Core\Extensions\Content\ContentTemplateBlock;

return [
    'index' => new ContentTemplate(
        name: 'Главная',
        file: 'index.tpl',
        params: [
            new ContentTemplateBlock('Слайдер', [
                new FieldDefinition(
                    name: 'slides',
                    class: FieldTable::class,
                    label: 'Слайды',
                    params: ['struct' => FieldTable::makeValue([], [
                        FieldTable::makeStruct('title', 'text', 'Заголовок'),
                        FieldTable::makeStruct('text', 'text', 'Описание'),
                        FieldTable::makeStruct('image', 'image', 'Картинка'),
                        FieldTable::makeStruct('buttonText', 'text', 'Текст кнопки'),
                        FieldTable::makeStruct('buttonLink', 'text', 'Ссылка кнопки'),
                    ])],
                ),
            ]),
            new ContentTemplateBlock('Преимущества', [
                new FieldDefinition(name: 'features_title', class: FieldString::class, label: 'Заголовок блока'),
                new FieldDefinition(
                    name: 'benefits',
                    class: FieldTable::class,
                    label: 'Преимущества',
                    params: ['struct' => FieldTable::makeValue([], [
                        FieldTable::makeStruct('title', 'text', 'Заголовок'),
                        FieldTable::makeStruct('text', 'text', 'Текст'),
                        FieldTable::makeStruct('icon', 'text', 'Иконка (имя из спрайта)'),
                    ])],
                ),
            ]),
            new ContentTemplateBlock('Связаться с нами', [
                new FieldDefinition(name: 'rform_title', class: FieldString::class, label: 'Заголовок'),
                new FieldDefinition(name: 'rform_desc', class: FieldText::class, label: 'Описание'),
                new FieldDefinition(name: 'rform_image', class: FieldString::class, label: 'Картинка (адрес)'),
            ]),
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
