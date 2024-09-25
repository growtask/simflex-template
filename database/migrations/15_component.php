<?php

use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('component', function (Schema\Table $c) {
            $c->id('component_id');
            $c->string('class');
            $c->string('name');
            $c->longText('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('component');
    }

    public function seed()
    {
        DB::query(
            "INSERT INTO component (component_id, class, name, params) VALUES ('1', ?, 'Материалы', 'a:2:{s:6:\"аег\";s:1:\"q\";s:6:\"groupp\";a:1:{s:4:\"jopa\";s:1:\"2\";}}')",
            ['\App\Extensions\Content\Content']
        );
        DB::query(
            "INSERT INTO component (component_id, class, name, params) VALUES ('2', ?, 'Авторизация', '')",
            ['\App\Extensions\Auth\Auth']
        );
    }
};