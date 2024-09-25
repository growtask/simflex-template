<?php
use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('module_item', function (Schema\Table $c) {
            $c->id('item_id');
            $c->integer('module_id')->foreignKey('module');
            $c->integer('menu_id');
            $c->string('posname')->index();
            $c->boolean('active')->setDefault(0);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->longText('params');
        });
    }

    public function down(Schema $s)
    {
        $s->dropTable('module_item');
    }

    public function seed()
    {
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('1', '1', NULL, 'mainmenu', '1', '1', 'Меню главное', 'a:4:{s:8:\"is_title\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:4:\"NULL\";s:8:\"is_child\";s:1:\"0\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('3', '3', NULL, 'content-outer-before', '1', '0', 'Хлебные крошки', 'a:3:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:8:\"cssclass\";s:0:\"\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('4', '4', NULL, 'footer', '1', '102', 'Копирайт', 'a:2:{s:7:\"content\";s:120:\"<p>2012 &copy; ДСМ Урал<br /> г. Пермь, ул. Екатерининская 59, тел.: (342) 204-40-64</p>\";s:8:\"is_title\";s:1:\"0\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('8', '4', NULL, 'footer', '1', '111', 'Счетчики', 'a:2:{s:7:\"content\";s:131:\"<p><img src=\"../../../theme/default/img/liveinternet.png\" alt=\"\" /> <img src=\"../../../theme/default/img/metrika.png\" alt=\"\" /></p>\";s:8:\"is_title\";s:1:\"0\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('9', '4', NULL, 'header-right', '1', '0', 'Контакты вверху', 'a:4:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:7:\"menu_id\";s:1:\"0\";s:7:\"content\";s:197:\"<div class=\"phone\"><span>(342)</span> 204-40-64</div>
<div style=\"margin-top: 4px; font-size: 12px;\">г. Пермь, <a href=\"/contacts/\">ул. Екатерининская 59</a>, оф. 204</div>\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('10', '1', NULL, 'footer', '1', '101', 'Меню сайта внизу страницы', 'a:1:{s:8:\"is_title\";s:1:\"0\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('11', '4', NULL, 'footer', '1', '103', 'Ссылка на разработчика', 'a:2:{s:7:\"content\";s:126:\"<p><a href=\"http://internet-menu.ru\">Создание сайта</a> &mdash; веб-студия Интернет Меню</p>\";s:8:\"is_title\";s:1:\"0\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('14', '1', NULL, 'aside', '1', '51', 'Меню дочернее', 'a:4:{s:8:\"is_title\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:4:\"NULL\";s:8:\"is_child\";s:1:\"1\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('15', '7', NULL, 'breadcrumbs', '1', '0', 'Хлебные крошки', '')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('16', '8', NULL, 'menu', '1', '0', 'Меню', 'a:6:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:7:\"menu_id\";s:1:\"0\";s:9:\"max_level\";s:1:\"0\";s:8:\"start_id\";s:1:\"0\";s:8:\"is_child\";s:1:\"0\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('17', '11', '31', 'content-before', '1', '116', 'Admin. Аккаунт', 'a:2:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"1\";}')");
        DB::query("INSERT INTO module_item (item_id, module_id, menu_id, posname, active, npp, name, params) VALUES ('18', '12', '34', 'content-before', '1', '117', 'Admin. Репозиторий', 'a:3:{s:8:\"is_title\";s:1:\"0\";s:7:\"is_wrap\";s:1:\"0\";s:8:\"cssclass\";s:0:\"\";}')");
    }
};