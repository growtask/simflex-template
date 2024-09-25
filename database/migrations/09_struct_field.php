<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('struct_field', function (Schema\Table $c) {
            $c->id('field_id');
            $c->string('name', 50);
            $c->string('class');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_field');
    }

    public function seed()
    {
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('1', 'Строка', ?)", ['\Simflex\Admin\Fields\FieldString']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('2', 'Целое число', ?)", ['\Simflex\Admin\Fields\FieldInt']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('3', 'Булевая переменная', ?)", ['\Simflex\Admin\Fields\FieldBool']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('4', 'Алиас', ?)", ['\Simflex\Admin\Fields\FieldAlias']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('5', 'Url-путь', ?)", ['\Simflex\Admin\Fields\FieldPath']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('6', 'Пароль', ?)", ['\Simflex\Admin\Fields\FieldPassword']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('7', 'Дата', ?)", ['\Simflex\Admin\Fields\FieldDate']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('8', 'Дата и время', ?)", ['\Simflex\Admin\Fields\FieldDateTime']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('9', 'Текст', ?)", ['\Simflex\Admin\Fields\FieldText']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('10', 'Файл', ?)", ['\Simflex\Admin\Fields\FieldFile']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('11', 'Изображение', ?)", ['\Simflex\Admin\Fields\FieldImage']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('14', 'Варианты', ?)", ['\Simflex\Admin\Fields\FieldEnum']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('15', '№ п/п', ?)", ['\Simflex\Admin\Fields\FieldNPP']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('16', 'Виртуальное поле', ?)", ['\Simflex\Admin\Fields\FieldVirtual']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('17', 'Время', ?)", ['\Simflex\Admin\Fields\FieldTime']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('18', 'Дробное число', ?)", ['\Simflex\Admin\Fields\FieldDouble']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('19', 'Связь многие ко многим', ?)", ['\Simflex\Admin\Fields\FieldMultiKey']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('20', 'Пароль видимый', ?)", ['\Simflex\Admin\Fields\FieldPasswordVisible']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('21', 'Таблица', ?)", ['\Simflex\Admin\Fields\FieldTable']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('22', 'Теги', ?)", ['\Simflex\Admin\Fields\FieldTags']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('23', 'Двусторонняя связь', ?)", ['\Simflex\Admin\Fields\FieldRelation']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('24', 'Виртуальная таблица', ?)", ['\Simflex\Admin\Fields\FieldVirtualTable']);
        DB::query("INSERT INTO struct_field (field_id, name, class) VALUES ('25', 'Строка с поиском', ?)", ['\App\Extensions\Catalog\Admin\Fields\FieldStringSelect']);
    }
};