<?php
use Simflex\Admin\Migration\Struct;
use \Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        $s->createTable('cron_log', function (Schema\Table $c) {
            $c->id('id');
            $c->integer('cron_id');
            $c->dateTime('datetime');
            $c->string('result', 2047);
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('cron_log');
    }
};