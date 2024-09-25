<?php
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->integer('component_id')->foreignKey('component');
            $c->boolean('active')->setDefault(0);
            $c->boolean('hidden')->setDefault(0);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('menu');
    }

    public function seed()
    {
        DB::query("INSERT INTO menu (menu_id, menu_pid, component_id, active, hidden, npp, name, link) VALUES ('1', NULL, 1, '1', '0', '1', 'Главная', '/')");
    }
};