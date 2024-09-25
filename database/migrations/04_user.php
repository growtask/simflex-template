<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('user', function (Schema\Table $c) {
            $c->id('user_id');
            $c->integer('role_id')->foreignKey('user_role')->setDefault(3);
            $c->boolean('active')->setDefault(1);
            $c->string('login')->unique(true);
            $c->string('password');
            $c->string('hash'); // deprecated
            $c->string('hash_admin'); // deprecated
            $c->string('email')->unique(true);
            $c->string('name');
            $c->string('code');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('user');
    }

    public function seed()
    {
        DB::query('DELETE FROM user');
        DB::query("INSERT INTO user (user_id, role_id, active, login, password, hash, hash_admin, email, name, code) VALUES ('1', '1', '1', 'dev', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 'dev@simflex.local', '', '')");
        DB::query("INSERT INTO user (user_id, role_id, active, login, password, hash, hash_admin, email, name, code) VALUES ('2', '2', '1', 'admin', '827ccb0eea8a706c4c34a16891f84e7b', '', '', 'admin@simflex.local', '', '')");
    }
};