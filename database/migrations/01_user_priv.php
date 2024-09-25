<?php

use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('user_priv', function (Schema\Table $c) {
            $c->id('priv_id');
            $c->boolean('active');
            $c->integer('npp');
            $c->string('name');
            $c->string('comment');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_priv');
    }

    public function seed()
    {
        DB::query('DELETE FROM user_priv');
        DB::query("INSERT INTO user_priv (priv_id, active, npp, name, comment) VALUES ('1', '1', '1', 'dev', 'Привилегия разработчика')");
        DB::query("INSERT INTO user_priv (priv_id, active, npp, name, comment) VALUES ('2', '1', '2', 'admin', 'Привилегия администратора')");
        DB::query("INSERT INTO user_priv (priv_id, active, npp, name, comment) VALUES ('3', '1', '2', 'simplex_admin', 'Доступ к административному интерфейсу')");
        DB::query("INSERT INTO user_priv (priv_id, active, npp, name, comment) VALUES ('4', '1', '3', 'debug', 'Показывать отладчик')");
        DB::query("INSERT INTO user_priv (priv_id, active, npp, name, comment) VALUES ('5', '1', '4', 'manager', 'Менеджер')");
    }
};