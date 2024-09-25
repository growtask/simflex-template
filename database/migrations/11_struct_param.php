<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('struct_param', function (Schema\Table $c) {
            $c->id('param_id');
            $c->integer('param_pid');
            $c->integer('table_id')->foreignKey('struct_table');
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('pos', 50);
            $c->string('name', 50);
            $c->string('label', 50);
            $c->text('default_value');
            $c->text('params');
            $c->integer('npp');
            $c->string('group_name');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_param');
    }

    public function seed()
    {
        DB::query('DELETE FROM struct_param');
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('1', NULL, '3', NULL, 'right', 'main', 'Параметры', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('2', '1', '3', '3', '', 'pk', 'Первичный ключ', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('3', '1', '3', '3', '', 'e2n', 'Преобразовать к NULL', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('4', '1', '3', '3', '', 'hidden', 'Скрытый', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('5', '1', '3', '2', '', 'width', 'Ширина столбца', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('6', '1', '3', '1', '', 'defaultValue', 'Значение по умолчанию', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('7', '1', '3', '3', '', 'required', 'Обязательно для заполнения', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('8', '1', '3', '3', '', 'filter', 'Добавить в фильтр', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('10', NULL, '12', NULL, 'right', 'content_main', 'Вывод данных', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('11', '10', '12', '1', '', 'tpl', 'Шаблон вывода', 'mod_list.tpl', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('12', '10', '12', '2', '', 'cnt_limit', 'Количество', '3', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('13', NULL, '12', NULL, 'right', 'content_view', 'Внешний вид', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('14', '13', '12', '3', '', 'date', 'Дата', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('15', '13', '12', '3', '', 'short', 'Анонс', '0', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('16', '13', '12', '3', '', 'more', 'Кнопка далее', '0', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('17', '13', '12', '1', '', 'more_text', 'Текст на кнопке далее', 'Читать далее', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('18', '1', '3', '1', '', 'onchange', 'on change', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('19', NULL, '4', NULL, 'right', 'main', 'Параметры', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('20', '19', '4', '1', '', 'defaultValue', 'Значение по умолчанию', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('21', NULL, '28', NULL, 'right', 'module_param_main', 'Параметры', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('22', '21', '28', '1', '', 'default_value', 'Значение по умолчанию', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('23', NULL, '25', NULL, 'right', 'module_item_main', 'Параметры', '', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('25', NULL, '25', '3', 'right', 'is_title', 'Показывать заголовок', '1', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('26', NULL, '25', '3', 'right', 'is_wrap', 'Выводить обертку', '1', '', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('27', '10', '12', '3', '', 'hide_title', 'Не показывать заголовок', '0', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:1:\"0\";}}', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('28', '1', '3', '3', '', 'readonly', 'Только чтение', '0', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('29', '1', '3', '1', '', 'style_cell', 'CSS стили ячейки', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('30', NULL, '25', '1', 'right', 'cssclass', 'CSS Класс', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('31', '1', '3', '2', '', 'screen_width', 'Показывать на мониторах до, px', '0', 'a:1:{s:4:\"main\";a:6:{s:12:\"defaultValue\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('32', '1', '3', '2', '', 'width_mob', 'Ширина столбца (мобильная)', '', 'a:1:{s:4:\"main\";a:6:{s:12:\"defaultValue\";s:0:\"\";s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}', '0', 'NULL')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('33', '1', '3', '1', '', 'pos', 'Позиция', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', '0', '')");
        DB::query("INSERT INTO struct_param (param_id, param_pid, table_id, field_id, pos, name, label, default_value, params, npp, group_name) VALUES ('34', '1', '3', '1', '', 'pos_group', 'Группа', '', 'a:1:{s:4:\"main\";a:1:{s:12:\"defaultValue\";s:0:\"\";}}', '0', '')");
    }
};