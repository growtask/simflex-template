<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('content_template', function (Schema\Table $c) {
            $c->id('template_id');
            $c->string('template_name');
            $c->string('template_path');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('content_template');
    }

    public function seed()
    {
        DB::query("INSERT INTO content_template (template_id, template_name, template_path) VALUES ('1', 'Главная', 'index.tpl')");
    }
};