<?php

use Simflex\Core\Models\StructField;
use Simflex\Core\Models\StructFieldParam;

return new class implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        $stringField = StructField::insertStatic([
            'name' => 'Строка',
            'class' => \Simflex\Admin\Fields\FieldString::class,
        ]);

        $boolField = StructField::insertStatic([
            'name' => 'Булевая переменная',
            'class' => \Simflex\Admin\Fields\FieldBool::class,
        ]);

        $intField = StructField::insertStatic([
            'name' => 'Целое число',
            'class' => \Simflex\Admin\Fields\FieldInt::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $intField->field_id,
                'name' => 'is_fk',
                'label' => 'Внешний ключ',
                'type_id' => $boolField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $intField->field_id,
                'name' => 'fk_table',
                'label' => 'Внешний ключ. Таблица',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $intField->field_id,
                'name' => 'fk_key',
                'label' => 'Внешний ключ. Ключ',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $intField->field_id,
                'name' => 'fk_label',
                'label' => 'Внешний ключ. Ярлык',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $intField->field_id,
                'name' => 'fk_is_pid',
                'label' => 'Внешний ключ. Поле PID',
                'type_id' => $boolField->field_id,
                'help' => '',
                'default_value' => ''
            ],
        ]);

        $aliasField = StructField::insertStatic([
            'name' => 'Алиас',
            'class' => \Simflex\Admin\Fields\FieldAlias::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $aliasField->field_id,
                'name' => 'source',
                'label' => 'Источник',
                'type_id' => $stringField->field_id,
                'help' => 'Из какого поля таблицы брать значение. Например: name',
                'default_value' => 'name'
            ]
        ]);

        StructField::insertStatic([
            'name' => 'Url-путь',
            'class' => \Simflex\Admin\Fields\FieldPath::class,
        ]);

        StructField::insertStatic([
            'name' => 'Пароль',
            'class' => \Simflex\Admin\Fields\FieldPassword::class,
        ]);

        StructField::insertStatic([
            'name' => 'Дата',
            'class' => \Simflex\Admin\Fields\FieldDate::class,
        ]);

        StructField::insertStatic([
            'name' => 'Дата и время',
            'class' => \Simflex\Admin\Fields\FieldDateTime::class,
        ]);

        $textField = StructField::insertStatic([
            'name' => 'Текст',
            'class' => \Simflex\Admin\Fields\FieldText::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $textField->field_id,
                'name' => 'editor_mini',
                'label' => 'Минимальный редактор',
                'type_id' => $boolField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $textField->field_id,
                'name' => 'editor_full',
                'label' => 'Полный редактор',
                'type_id' => $boolField->field_id,
                'help' => '',
                'default_value' => ''
            ]
        ]);

        $fileField = StructField::insertStatic([
            'name' => 'Файл',
            'class' => \Simflex\Admin\Fields\FieldFile::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $fileField->field_id,
                'name' => 'path',
                'label' => 'Адрес',
                'type_id' => $stringField->field_id,
                'help' => '/uf/files/{указанный адрес}',
                'default_value' => ''
            ]
        ]);

        $imageField = StructField::insertStatic([
            'name' => 'Изображение',
            'class' => \Simflex\Admin\Fields\FieldImage::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $imageField->field_id,
                'name' => 'path',
                'label' => 'Адрес',
                'type_id' => $stringField->field_id,
                'help' => '/uf/images/{указанный адрес}',
                'default_value' => ''
            ],
            [
                'field_id' => $imageField->field_id,
                'name' => 'small',
                'label' => 'Малый размер',
                'type_id' => $stringField->field_id,
                'help' => 'Формат 200x150. Точный размер - 200x150x1',
                'default_value' => ''
            ],
            [
                'field_id' => $imageField->field_id,
                'name' => 'medium',
                'label' => 'Средний размер',
                'type_id' => $stringField->field_id,
                'help' => 'Формат 400x300. Точный размер - 400x300x1',
                'default_value' => ''
            ],
            [
                'field_id' => $imageField->field_id,
                'name' => 'large',
                'label' => 'Большой размер',
                'type_id' => $stringField->field_id,
                'help' => 'Формат 640x480. Точный размер - 640x480x1',
                'default_value' => ''
            ]
        ]);

        $enumField = StructField::insertStatic([
            'name' => 'Варианты',
            'class' => \Simflex\Admin\Fields\FieldEnum::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $enumField->field_id,
                'name' => 'enum',
                'label' => 'Варианты (key=value;;)',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ]
        ]);

        StructField::insertStatic([
            'name' => '№ п/п',
            'class' => \Simflex\Admin\Fields\FieldNPP::class,
        ]);

        $virtualField = StructField::insertStatic([
            'name' => 'Виртуальное поле',
            'class' => \Simflex\Admin\Fields\FieldVirtual::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $virtualField->field_id,
                'name' => 'subquery',
                'label' => 'Подзапрос',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $virtualField->field_id,
                'name' => 'text',
                'label' => 'Текст',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $virtualField->field_id,
                'name' => 'href',
                'label' => 'Ссылка',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $virtualField->field_id,
                'name' => 'in_modal',
                'label' => 'Открыть в модальном окне',
                'type_id' => $boolField->field_id,
                'help' => '',
                'default_value' => ''
            ]
        ]);

        $timeField = StructField::insertStatic([
            'name' => 'Время',
            'class' => \Simflex\Admin\Fields\FieldTime::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $timeField->field_id,
                'name' => 'use_seconds',
                'label' => 'С секундами',
                'type_id' => $boolField->field_id,
                'help' => 'Учитывать секунды',
                'default_value' => '0'
            ]
        ]);

        $doubleField = StructField::insertStatic([
            'name' => 'Дробное число',
            'class' => \Simflex\Admin\Fields\FieldDouble::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $doubleField->field_id,
                'name' => 'decimals',
                'label' => 'Число знаков после запятой',
                'type_id' => $intField->field_id,
                'help' => '0 - автоматически',
                'default_value' => '0'
            ],
            [
                'field_id' => $doubleField->field_id,
                'name' => 'dec_point',
                'label' => 'Разделитель дробной части',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => '.'
            ],
            [
                'field_id' => $doubleField->field_id,
                'name' => 'thousands_sep',
                'label' => 'Разделитель порядков',
                'type_id' => $stringField->field_id,
                'help' => ' ',
                'default_value' => ' '
            ]
        ]);

        $manyField = StructField::insertStatic([
            'name' => 'Связь многие ко многим',
            'class' => \Simflex\Admin\Fields\FieldMultiKey::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $manyField->field_id,
                'name' => 'table_values',
                'label' => 'Таблица сущностей',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $manyField->field_id,
                'name' => 'table_relations',
                'label' => 'Таблица связей',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $manyField->field_id,
                'name' => 'key',
                'label' => 'Ключ таблицы сущностей',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $manyField->field_id,
                'name' => 'key_alias',
                'label' => 'Поле-ярлык у сущности',
                'type_id' => $stringField->field_id,
                'help' => 'Например name у catalog_category',
                'default_value' => ''
            ]
        ]);

        StructField::insertStatic([
            'name' => 'Пароль видимый',
            'class' => \Simflex\Admin\Fields\FieldPasswordVisible::class,
        ]);

        $tableField = StructField::insertStatic([
            'name' => 'Таблица',
            'class' => \Simflex\Admin\Fields\FieldTable::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $tableField->field_id,
                'name' => 'struct',
                'label' => 'Параметры',
                'type_id' => $tableField->field_id,
                'help' => '',
                'default_value' => json_encode([
                    's' => [
                        [
                            'n' => 'n',
                            't' => 'text',
                            'l' => 'Имя',
                            'v' => '',
                            'e' => ''
                        ],
                        [
                            'n' => 't',
                            't' => 'combo',
                            'l' => 'Тип',
                            'v' => 'text',
                            'e' => 'text=Текст,,int=Число,,combo=Список,,editor=Редактор,,image=Изображение,,file=Файл'
                        ],
                        [
                            'n' => 'l',
                            't' => 'text',
                            'l' => 'Заголовок',
                            'v' => '',
                            'e' => ''
                        ],
                        [
                            'n' => 'v',
                            't' => 'text',
                            'l' => 'Значение',
                            'v' => '',
                            'e' => ''
                        ],
                        [
                            'n' => 'e',
                            't' => 'text',
                            'l' => 'Дополнительно',
                            'v' => '',
                            'e' => ''
                        ]
                    ],
                    'v' => []
                ], JSON_UNESCAPED_UNICODE)
            ]
        ]);

        StructField::insertStatic([
            'name' => 'Теги',
            'class' => \Simflex\Admin\Fields\FieldTags::class,
        ]);

        $relationField = StructField::insertStatic([
            'name' => 'Двусторонняя связь',
            'class' => \Simflex\Admin\Fields\FieldRelation::class,
        ]);

        StructFieldParam::bulkInsert([
            [
                'field_id' => $relationField->field_id,
                'name' => 'relation',
                'label' => 'Таблица связей',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $relationField->field_id,
                'name' => 'left',
                'label' => 'Левая колонка',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $relationField->field_id,
                'name' => 'right',
                'label' => 'Правая колонка',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $relationField->field_id,
                'name' => 'table',
                'label' => 'Таблица сущностей',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ],
            [
                'field_id' => $relationField->field_id,
                'name' => 'name',
                'label' => 'Ярлык',
                'type_id' => $stringField->field_id,
                'help' => '',
                'default_value' => ''
            ]
        ]);

        StructField::insertStatic([
            'name' => 'Виртуальная таблица',
            'class' => \Simflex\Admin\Fields\FieldVirtualTable::class,
        ]);

        StructField::insertStatic([
            'name' => 'Строка с поиском',
            'class' => \App\Extensions\Catalog\Admin\Fields\FieldStringSelect::class,
        ]);
    }
};