<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('user_role_priv', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('role_id')->foreignKey('user_role');
            $c->integer('priv_id')->foreignKey('user_priv');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user_role_priv');
    }

    public function seed()
    {
        DB::query('DELETE FROM user_role_priv');
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('1', '1', '1')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('2', '1', '2')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('3', '1', '3')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('4', '1', '4')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('5', '2', '2')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('6', '2', '4')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('7', '2', '3')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('8', '2', '5')");
        DB::query("INSERT INTO user_role_priv (id, role_id, priv_id) VALUES ('9', '1', '5')");
    }
};