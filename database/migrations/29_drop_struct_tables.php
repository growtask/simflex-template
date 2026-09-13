<?php

use Simflex\Core\DB;
use Simflex\Core\DB\Schema;

return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s)
    {
        DB::query('SET FOREIGN_KEY_CHECKS = 0');
        DB::query('DROP TABLE IF EXISTS struct_data');
        DB::query('DROP TABLE IF EXISTS struct_param');
        DB::query('DROP TABLE IF EXISTS struct_field_param');
        DB::query('DROP TABLE IF EXISTS struct_table_right');
        DB::query('DROP TABLE IF EXISTS struct_field');
        DB::query('DROP TABLE IF EXISTS struct_table');
        DB::query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down(Schema $s)
    {
    }
};
