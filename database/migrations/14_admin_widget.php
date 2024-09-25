<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('admin_widget', function (Schema\Table $c) {
            $c->id('widget_id');
            $c->boolean('active')->setDefault(1);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('class');
            $c->integer('priv_id')->foreignKey('user_priv');
            $c->string('param');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('admin_widget');
    }
};