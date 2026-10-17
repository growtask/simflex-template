<?php

/*
 * Page templates: key => \Simflex\Extensions\Content\ContentTemplate. A page refers to its template by the key.
 * file: relative to Extensions/Content/tpl. params: additional fields of the page form, grouped in blocks;
 * their values are in $content->params by the field name.
 * The fields declared by the Layout components of a page can be generated: ./sf layout/generate /page/path
 */

use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldText;
use Simflex\Admin\Structure\Data\FieldDefinition;
use Simflex\Admin\Structure\Data\FieldParams;
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
                    params: ['main' => new FieldParams(editorFull: true)],
                ),
                new FieldDefinition(name: 'seo_title_2', class: FieldString::class, label: 'SEO заголовок 2'),
                new FieldDefinition(
                    name: 'seo_text_2',
                    class: FieldText::class,
                    label: 'SEO текст 2',
                    params: ['main' => new FieldParams(editorFull: true)],
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
