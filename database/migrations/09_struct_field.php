<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
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
};