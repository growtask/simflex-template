<?php

use Simflex\Core\DB;
use Simflex\Core\DB\Schema;

/**
 * Ссылки admin_menu хранятся относительно корня админки (адрес админки задаётся в конфиге: ADMIN_PATH),
 * поэтому префикс /admin из них убирается: /admin/content/ => /content/
 */
return new class implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        DB::query("UPDATE `admin_menu` SET `link` = SUBSTRING(`link`, 7) WHERE `link` LIKE '/admin/%'");
        DB::query("UPDATE `admin_menu` SET `link` = '/' WHERE `link` = '/admin'");
    }

    public function down(Schema $s): void
    {
        DB::query("UPDATE `admin_menu` SET `link` = CONCAT('/admin', `link`) WHERE `link` LIKE '/%'");
    }
};
