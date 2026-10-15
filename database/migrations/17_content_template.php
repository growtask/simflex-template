<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('content_template', function (Schema\Table $c) {
            $c->id('template_id');
            $c->string('template_name');
            $c->string('template_path');
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('content_template');
    }
};