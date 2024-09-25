<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('struct_field_param', function (Schema\Table $c) {
            $c->id('fp_id');
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name');
            $c->string('label');
            $c->integer('type_id')->foreignKey('struct_field', 'field_id');
            $c->string('help');
            $c->text('default_value');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_field_param');
    }

    public function seed()
    {
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('1', '2', 'is_fk', 'Внешний ключ', '3', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('2', '2', 'fk_table', 'Внешний ключ. Таблица', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('3', '2', 'fk_key', 'Внешний ключ. Ключ', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('4', '2', 'fk_label', 'Внешний ключ. Ярлык', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('6', '2', 'fk_is_pid', 'Внешний ключ. Поле PID', '3', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('9', '9', 'editor_mini', 'Минимальный редактор', '3', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('10', '9', 'editor_full', 'Полный редактор', '3', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('11', '16', 'subquery', 'Подзапрос', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('12', '16', 'text', 'Текст', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('13', '16', 'href', 'Ссылка', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('14', '10', 'path', 'Адрес', '1', '/uf/files/{указанный адрес}', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('15', '11', 'path', 'Адрес', '1', '/uf/images/{указанный адрес}', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('16', '11', 'small', 'Малый размер', '1', 'Формат 200x150. Точный размер - 200x150x1', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('17', '11', 'medium', 'Средний размер', '1', 'Формат 400x300. Точный размер - 400x300x1', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('18', '11', 'large', 'Большой размер', '1', 'Формат 640x480. Точный размер - 640x480x1', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('19', '4', 'source', 'Источник', '1', 'Из какого поля таблицы брать значение. Например: name', 'name')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('20', '16', 'in_modal', 'Открыть в модальном окне', '3', '', '0')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('21', '18', 'decimals', 'Число знаков после запятой', '2', '0 - автоматически', '0')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('22', '18', 'dec_point', 'Разделитель дробной части', '1', '', '.')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('23', '18', 'thousands_sep', 'Разделитель порядков', '1', '', ' ')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('24', '17', 'use_seconds', 'С секундами', '3', 'Учитывать секунды', '0')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('25', '19', 'table_values', 'Таблица сущностей', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('26', '19', 'table_relations', 'Таблица связей', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('27', '19', 'key', 'Ключ таблицы сущностей', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('28', '19', 'key_alias', 'Поле-ярлык у сущности', '1', 'Например name у catalog_category', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('29', '21', 'struct', 'Параметры', '21', '', '{
    \"s\": [
        {
            \"n\": \"n\",
            \"t\": \"text\",
            \"l\": \"Имя\",
            \"v\": \"\",
            \"e\": \"\"
        },
        {
            \"n\": \"t\",
            \"t\": \"combo\",
            \"l\": \"Тип\",
            \"v\": \"text\",
            \"e\": \"text=Текст,,int=Число,,combo=Список,,editor=Редактор,,image=Изображение,,file=Файл\"
        },
        {
            \"n\": \"l\",
            \"t\": \"text\",
            \"l\": \"Заголовок\",
            \"v\": \"\",
            \"e\": \"\"
        },
        {
            \"n\": \"v\",
            \"t\": \"text\",
            \"l\": \"Значение\",
            \"v\": \"\",
            \"e\": \"\"
        },
        {
            \"n\": \"e\",
            \"t\": \"text\",
            \"l\": \"Дополнительно\",
            \"v\": \"\",
            \"e\": \"\"
        }
    ],
    \"v\": []
}')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('30', '23', 'relation', 'Таблица связей', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('31', '23', 'left', 'Левая колонка', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('32', '23', 'right', 'Правая колонка', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('33', '23', 'table', 'Таблица сущностей', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('34', '23', 'name', 'Ярлык', '1', '', '')");
        DB::query("INSERT INTO struct_field_param (fp_id, field_id, name, label, type_id, help, default_value) VALUES ('35', '14', 'enum', 'Варианты (key=value;;)', '1', '', '')");
    }
};