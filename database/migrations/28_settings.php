<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('settings', function (Schema\Table $c) {
            $c->id('setting_id');
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('alias');
            $c->string('value');
            $c->enum('type', ['int', 'string', 'bool', 'enum'])->setDefault('string');
            $c->string('enum_values');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('settings');
    }

    public function seed()
    {
        }
};