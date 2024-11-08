<?php
return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
        $indexTemplate = \Simflex\Core\Models\ContentTemplate::insertStatic([
            'template_name' => 'Главная',
            'template_path' => 'index.tpl'
        ]);

        $stringField = \Simflex\Core\Models\StructField::byClass(\Simflex\Admin\Fields\FieldString::class);
        $textField = \Simflex\Core\Models\StructField::byClass(\Simflex\Admin\Fields\FieldText::class);

        \Simflex\Core\Models\ContentTemplateParam::bulkInsert([
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'left',
                'field_id' => $stringField->field_id,
                'name' => 'seo_title_1',
                'label' => 'SEO заголовок 1',
                'npp' => 10000,
                'help' => '',
                'params' => serialize([]),
                'default_value' => '',
                'group_name' => 'SEO'
            ],
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'left',
                'field_id' => $textField->field_id,
                'name' => 'seo_text_1',
                'label' => 'SEO текст 1',
                'npp' => 10000,
                'help' => '',
                'params' => serialize(['main' => ['editor_mini' => 0, 'editor_full' => 1]]),
                'default_value' => '',
                'group_name' => 'SEO'
            ],
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'left',
                'field_id' => $stringField->field_id,
                'name' => 'seo_title_2',
                'label' => 'SEO заголовок 2',
                'npp' => 10000,
                'help' => '',
                'params' => serialize([]),
                'default_value' => '',
                'group_name' => 'SEO'
            ],
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'left',
                'field_id' => $textField->field_id,
                'name' => 'seo_text_2',
                'label' => 'SEO текст 2',
                'npp' => 10000,
                'help' => '',
                'params' => serialize(['main' => ['editor_mini' => 0, 'editor_full' => 1]]),
                'default_value' => '',
                'group_name' => 'SEO'
            ],
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'right',
                'field_id' => $stringField->field_id,
                'name' => 'meta_title',
                'label' => 'Заголовок',
                'npp' => 11000,
                'help' => '',
                'params' => serialize([]),
                'default_value' => '',
                'group_name' => 'Мета'
            ],
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'right',
                'field_id' => $textField->field_id,
                'name' => 'meta_kw',
                'label' => 'Ключевые слова',
                'npp' => 11000,
                'help' => '',
                'params' => serialize(['main' => ['editor_mini' => 0, 'editor_full' => 0]]),
                'default_value' => '',
                'group_name' => 'Мета'
            ],
            [
                'template_id' => $indexTemplate->template_id,
                'param_pid' => null,
                'position' => 'right',
                'field_id' => $textField->field_id,
                'name' => 'meta_de',
                'label' => 'Описание',
                'npp' => 11000,
                'help' => '',
                'params' => serialize(['main' => ['editor_mini' => 0, 'editor_full' => 0]]),
                'default_value' => '',
                'group_name' => 'Мета'
            ]
        ]);

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
                'template_id' => $indexTemplate->template_id,
                'npp' => 10
            ]
        ]);
    }
};