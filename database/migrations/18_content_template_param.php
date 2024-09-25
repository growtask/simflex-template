<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('content_template_param', function (Schema\Table $c) {
            $c->id('ctp_id');
            $c->integer('template_id')->foreignKey('content_template');
            $c->integer('param_pid');
            $c->string('position', 50);
            $c->integer('field_id')->foreignKey('struct_field');
            $c->string('name', 50);
            $c->string('label', 50);
            $c->integer('npp')->setDefault(0);
            $c->string('help')->setDefault('');
            $c->longText('params');
            $c->string('default_value');
            $c->string('group_name');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('content_template_param');
    }

    public function seed()
    {
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('1', '1', NULL, 'left', '1', 'seo_title_1', 'SEO заголовок 1', '10000', '', 'a:0:{}', '', 'SEO')");
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('2', '1', NULL, 'left', '9', 'seo_text_1', 'SEO текст 1', '10000', '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}', '', 'SEO')");
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('3', '1', NULL, 'left', '1', 'seo_title_2', 'SEO заголовок 2', '10000', '', 'a:0:{}', '', 'SEO')");
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('4', '1', NULL, 'left', '9', 'seo_text_2', 'SEO текст 2', '10000', '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"1\";}}', '', 'SEO')");
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('5', '1', NULL, 'right', '1', 'meta_title', 'Заголовок', '11000', '', 'a:0:{}', '', 'Мета')");
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('6', '1', NULL, 'right', '9', 'meta_kw', 'Ключевые слова', '11000', '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}', '', 'Мета')");
        DB::query("INSERT INTO content_template_param (ctp_id, template_id, param_pid, position, field_id, name, label, npp, help, params, default_value, group_name) VALUES ('7', '1', NULL, 'right', '9', 'meta_de', 'Описание', '11000', '', 'a:1:{s:4:\"main\";a:2:{s:11:\"editor_mini\";s:1:\"0\";s:11:\"editor_full\";s:1:\"0\";}}', '', 'Мета')");
    }
};