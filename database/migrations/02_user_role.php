<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('user_role', function (Schema\Table $c) {
            $c->id('role_id');
            $c->integer('priv_id')->foreignKey('user_priv');
            $c->boolean('active');
            $c->integer('npp');
            $c->string('name');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_role');
    }

    public function seed()
    {
        DB::query('DELETE FROM user_role');
        DB::query("INSERT INTO user_role (role_id, priv_id, active, npp, name) VALUES ('1', '1', '1', '1', 'Разработчик')");
        DB::query("INSERT INTO user_role (role_id, priv_id, active, npp, name) VALUES ('2', '2', '1', '2', 'Администратор')");
        DB::query("INSERT INTO user_role (role_id, priv_id, active, npp, name) VALUES ('3', '2', '1', '3', 'Пользователь')");
        DB::query("INSERT INTO user_role (role_id, priv_id, active, npp, name) VALUES ('4', '2', '1', '4', 'Менеджер')");
    }
};