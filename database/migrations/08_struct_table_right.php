<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('struct_table_right', function (Schema\Table $c) {
            $c->integer('table_id');
            $c->integer('role_id');
            $c->boolean('can_add');
            $c->boolean('can_edit');
            $c->boolean('can_delete');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('struct_table_right');
    }
};