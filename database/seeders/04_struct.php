<?php

use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldInt;
use Simflex\Admin\Fields\FieldString;
use Simflex\Admin\Fields\FieldVirtual;
use Simflex\Core\Models\StructData;
use Simflex\Core\Models\StructField;
use Simflex\Core\Models\StructTable;
use Simflex\Core\Models\UserPriv;

return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
        $devPriv = UserPriv::byName('dev');

        $stringField = StructField::byClass(FieldString::class);
        $intField = StructField::byClass(FieldInt::class);
        $boolField = StructField::byClass(FieldBool::class);
        $virtualField = StructField::byClass(FieldVirtual::class);

        $structTable = StructTable::insertStatic([
            'name' => 'struct_table',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructData::bulkInsert([
            [
                'npp' => 1,
                'table_id' => $structTable->table_id,
                'field_id' => $intField->field_id,
                'name' => 'table_id',
                'label' => 'ID',
                'help' => '',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '1',
                        'e2n' => '0',
                        'hidden' => '1',
                        'width' => '60',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '40',
                        'pos' => '',
                        'pos_group' => '',
                        'is_fk' => '0',
                        'fk_table' => '',
                        'fk_key' => '',
                        'fk_label' => '',
                        'fk_is_pid' => '0',
                    ],
                ]),
            ],
            [
                'npp' => 2,
                'table_id' => $structTable->table_id,
                'field_id' => $stringField->field_id,
                'name' => 'name',
                'label' => 'Название',
                'help' => 'Название таблицы в базе данных',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '0',
                        'hidden' => '0',
                        'width' => '1',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '200',
                        'pos' => '',
                        'pos_group' => '',
                    ],
                ]),
            ],
            [
                'npp' => 3,
                'table_id' => $structTable->table_id,
                'field_id' => $stringField->field_id,
                'name' => 'order_by',
                'label' => 'Сортировать по',
                'help' => 'Название поля, по которому сортировать по умолчанию (прим. npp - номер по порядку)',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '1',
                        'hidden' => '0',
                        'width' => '200',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '0',
                        'pos' => '',
                        'pos_group' => '',
                    ],
                ]),
            ],
            [
                'npp' => 4,
                'table_id' => $structTable->table_id,
                'field_id' => $boolField->field_id,
                'name' => 'order_desc',
                'label' => 'По убыванию',
                'help' => 'Сортировать от большего к меньшему',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '1',
                        'hidden' => '0',
                        'width' => '150',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '0',
                        'pos' => '',
                        'pos_group' => '',
                    ],
                ]),
            ],
            [
                'npp' => 5,
                'table_id' => $structTable->table_id,
                'field_id' => $virtualField->field_id,
                'name' => 'loadstruct',
                'label' => 'Загрузить структуру',
                'help' => '',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '0',
                        'hidden' => '0',
                        'width' => '180',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'fk' => '',
                        'onchange' => '',
                        'subquery' => '',
                        'text' => 'Загрузить',
                        'href' => '?action=loadstruct&table_id={table_id}',
                    ],
                ]),
            ],
            [
                'npp' => 6,
                'table_id' => $structTable->table_id,
                'field_id' => $intField->field_id,
                'name' => 'priv_add',
                'label' => 'Привилегия добавления',
                'help' => 'Обычно admin',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '1',
                        'hidden' => '0',
                        'width' => '150',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '0',
                        'pos' => '',
                        'pos_group' => '',
                        'is_fk' => '1',
                        'fk_table' => 'user_priv',
                        'fk_key' => 'priv_id',
                        'fk_label' => 'name',
                        'fk_is_pid' => '0',
                    ],
                ]),
            ],
            [
                'npp' => 7,
                'table_id' => $structTable->table_id,
                'field_id' => $intField->field_id,
                'name' => 'priv_delete',
                'label' => 'Привилегия удаления',
                'help' => 'Обычно admin',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '1',
                        'hidden' => '0',
                        'width' => '150',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '0',
                        'pos' => '',
                        'pos_group' => '',
                        'is_fk' => '1',
                        'fk_table' => 'user_priv',
                        'fk_key' => 'priv_id',
                        'fk_label' => 'name',
                        'fk_is_pid' => '0',
                    ],
                ]),
            ],[
                'npp' => 8,
                'table_id' => $structTable->table_id,
                'field_id' => $intField->field_id,
                'name' => 'priv_edit',
                'label' => 'Привилегия изменения',
                'help' => 'Обычно admin',
                'placeholder' => '',
                'params' => serialize([
                    'main' => 	[
                        'pk' => '0',
                        'e2n' => '1',
                        'hidden' => '0',
                        'width' => '150',
                        'defaultValue' => '',
                        'required' => '0',
                        'filter' => '0',
                        'onchange' => '',
                        'readonly' => '0',
                        'style_cell' => '',
                        'screen_width' => '0',
                        'width_mob' => '0',
                        'pos' => '',
                        'pos_group' => '',
                        'is_fk' => '1',
                        'fk_table' => 'user_priv',
                        'fk_key' => 'priv_id',
                        'fk_label' => 'name',
                        'fk_is_pid' => '0',
                    ],
                ]),
            ],
        ]);

        StructTable::insertStatic([
            'name' => 'struct_field',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'struct_data',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'struct_param',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'admin_menu',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'user_priv',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'user_role',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'user',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'user_role_priv',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'menu',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'component',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'content',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => \Simflex\Extensions\Content\Admin\AdminContent::class,
        ]);

        StructTable::insertStatic([
            'name' => 'settings',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'struct_field_param',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'module',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'module_item',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'seo',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'module_param',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'component_param',
            'order_by' => 'npp',
            'order_desc' => false,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'log',
            'order_by' => 'log_id',
            'order_desc' => true,
            'priv_add' => null,
            'priv_edit' => null,
            'priv_delete' => null,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'cron',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => $devPriv->priv_id,
            'priv_edit' => $devPriv->priv_id,
            'priv_delete' => $devPriv->priv_id,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'user_priv_personal',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => $devPriv->priv_id,
            'priv_edit' => $devPriv->priv_id,
            'priv_delete' => $devPriv->priv_id,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'content_template',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => $devPriv->priv_id,
            'priv_edit' => $devPriv->priv_id,
            'priv_delete' => $devPriv->priv_id,
            'class' => '',
        ]);

        StructTable::insertStatic([
            'name' => 'content_template_param',
            'order_by' => '',
            'order_desc' => false,
            'priv_add' => $devPriv->priv_id,
            'priv_edit' => $devPriv->priv_id,
            'priv_delete' => $devPriv->priv_id,
            'class' => '',
        ]);
    }
};