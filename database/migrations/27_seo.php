<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('seo', function (Schema\Table $c) {
            $c->id('seo_id');
            $c->integer('seo_pid');
            $c->string('link');
            $c->string('title');
            $c->text('description');
            $c->text('keywords');
            $c->text('metatags');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('seo');
    }
};