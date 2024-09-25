<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('module_param', function (Schema\Table $c) {
            $c->id('mp_id');
            $c->integer('module_id')->foreignKey('module');
            $c->integer('param_pid');
            $c->string('position', 50);
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name', 50);
            $c->string('label', 50);
            $c->integer('npp')->setDefault(0);
            $c->string('help')->setDefault('');
            $c->longText('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('module_param');
    }

    public function seed()
    {
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('1', '2', NULL, 'right', NULL, 'content_data', 'Вывод данных', '0', '', '')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('2', '2', '1', '', '2', 'content_id', 'Раздел', '0', '', 'a:1:{s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:7:\"content\";s:6:\"fk_key\";s:10:\"content_id\";s:8:\"fk_label\";s:5:\"title\";s:9:\"fk_is_pid\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('3', '2', '1', '', '1', 'tpl', 'Шаблон вывода', '1', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:12:\"mod_list.tpl\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('4', '2', '1', '', '2', 'cnt_limit', 'Количество', '2', '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"3\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('5', '2', NULL, 'right', NULL, 'module_param_view', 'Внешний вид', '1', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('6', '2', '5', '', '3', 'date', 'Дата', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('7', '2', '5', '', '3', 'short', 'Анонс', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('8', '2', '5', '', '3', 'more', 'Кнопка далее', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('9', '2', '5', '', '1', 'more_text', 'Кнопка далее', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:23:\"Читать далее\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('10', '4', NULL, 'left', NULL, 'module_param_content', 'Контент', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('11', '4', '10', '', '9', 'content', 'Текст', '0', '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('12', '8', NULL, 'right', '2', 'max_level', 'Макс. ур. вложенности', '0', '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('13', '8', NULL, 'right', '2', 'start_id', 'Выводить подразделы', '0', '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('14', '8', NULL, 'right', '3', 'is_child', 'Дочернее меню', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('15', '1', NULL, '', '2', 'start_id', 'Начальный пункт меню', '0', '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"1\";s:8:\"fk_table\";s:4:\"menu\";s:6:\"fk_key\";s:7:\"menu_id\";s:8:\"fk_label\";s:4:\"name\";s:9:\"fk_is_pid\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('16', '1', NULL, '', '2', 'max_level', 'Максимальный уровень вложенности', '0', '0 - без вложенных меню', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}s:4:\"main\";a:5:{s:5:\"is_fk\";s:1:\"0\";s:8:\"fk_table\";s:0:\"\";s:6:\"fk_key\";s:0:\"\";s:8:\"fk_label\";s:0:\"\";s:9:\"fk_is_pid\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('17', '1', NULL, '', '3', 'is_child', 'Выводить дочернее меню', '0', 'В текущем разделе выводить внутренние подразделы', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('21', '13', NULL, 'left', NULL, 'module_param_content', 'Контент', '0', '', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('22', '13', '21', '', '9', 'content', 'Текст', '0', '', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('24', '14', NULL, 'right', '1', 'submit_button_text', 'Кнопка', '1', 'Текст на кнопке \"отправить\"', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:18:\"Отправить\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('25', '14', NULL, 'right', '9', 'fields', 'Поля формы', '2', 'формат: название:тип:{обязательно 1}, пример ФИО:string, Почта:email:1', 'a:2:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}')");
        DB::query("INSERT INTO module_param (mp_id, module_id, param_pid, position, field_id, name, label, npp, help, params) VALUES ('26', '14', NULL, 'right', '1', 'form_tpl', 'Шаблон формы', '4', 'например awesomeForm.tpl, складывать в Extensions/Form/tpl', 'a:1:{s:17:\"module_param_main\";a:1:{s:13:\"default_value\";s:0:\"\";}}')");
    }
};