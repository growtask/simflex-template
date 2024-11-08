<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('content', function (Schema\Table $c) {
            $c->id('content_id');
            $c->integer('pid');
            $c->boolean('active')->setDefault(0);
            $c->date('date');
            $c->string('title');
            $c->string('alias');
            $c->string('path');
            $c->text('short');
            $c->longText('text');
            $c->longText('params');
            $c->string('file');
            $c->string('photo');
            $c->integer('template_id')->foreignKey('content_template');
            $c->integer('npp');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('content');
    }
};