<?php

use Simflex\Admin\Migration\Struct;
use Simflex\Core\DB;
use \Simflex\Core\DB\Schema;

return new class extends \Simflex\Core\DB\SeededMigration {
    public function up(Schema $s)
    {
        $s->createTable('admin_menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->integer('priv_id')->foreignKey('user_priv');
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
            $c->string('model');
            $c->string('icon');
            $c->boolean('hidden')->setDefault(0);
        });
        // todo: add keys post-factum
    }

    public function down(Schema $s)
    {
        $s->dropTable('admin_menu');
    }

    public function seed()
    {
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('1', NULL, '1', '1', 'Управление', '/admin/admin/', '', 'home', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('2', '1', '1', '1', 'Меню', '/admin/admin/menu/', 'admin_menu', 'settings-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('3', '1', '2', '3', 'Роли', '/admin/admin/role/', 'user_role', 'users-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('4', '1', '1', '6', 'Привилегии', '/admin/admin/priv/', 'user_priv', 'fingerprint-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('5', '1', '2', '2', 'Пользователи', '/admin/admin/user/', 'user', 'user-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('6', '1', '2', '7', 'Права доступа', '/admin/admin/rights/', 'user_role_priv', 'file-shield-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('7', NULL, '2', '2', 'Настройки сайта', '/admin/site/', '', 'settings', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('9', '7', '1', '1', 'Меню', '/admin/site/menu/', 'menu', 'user-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('10', '7', '1', '2', 'Модули', '/admin/site/module/', 'module_item', 'puzzle-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('11', '1', '1', '3', 'Компоненты', '/admin/admin/component/', 'component', 'container-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('12', NULL, '2', '3', 'Редактор страниц', '/admin/content/', 'content', 'edit', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('14', '7', '2', '8', 'Настройки', '/admin/site/settings/', 'settings', 'settings-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('15', '7', '1', '9', 'SEO', '/admin/site/seo/', 'seo', 'chart-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('22', '1', '1', '2', 'Структура', '/admin/admin/structure/', 'struct_data', 'layout-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('23', '22', '1', '2', 'Типы полей', '/admin/admin/structure/fields/', 'struct_field', 'puzzle', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('24', '22', '1', '1', 'Таблицы БД', '/admin/admin/structure/tables/', 'struct_table', 'puzzle', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('25', '22', '1', '3', 'Параметры таблиц', '/admin/admin/structure/params/', 'struct_param', 'puzzle', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('26', '22', '1', '5', 'Поля в таблицах', '/admin/admin/structure/', 'struct_data', 'puzzle', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('27', '22', '1', '4', 'Параметры полей', '/admin/admin/structure/field_param/', 'struct_field_param', 'puzzle', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('28', '1', '1', '3', 'Модули', '/admin/admin/module/', 'module', 'puzzle-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('29', '28', '1', '5', 'Параметры модулей', '/admin/admin/module/param/', 'module_param', '', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('30', '11', '1', '4', 'Параметры компонентов', '/admin/admin/component_param/', 'component_param', '', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('31', NULL, '3', '57', 'Аккаунт', '/admin/account/', '', 'user', '1')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('35', '1', '1', '54', 'Cron', '/admin/cron/', 'cron', 'calendar-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('36', '6', '2', '8', 'Персональные права', '/admin/user_priv_personal/', 'user_priv_personal', '', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('37', '1', '1', '50', 'Шаблоны страниц', '/admin/admin/content_template/', 'content_template', 'layout-mini', '0')");
        DB::query("INSERT INTO admin_menu (menu_id, menu_pid, priv_id, npp, name, link, model, icon, hidden) VALUES ('38', '37', '1', '51', 'Параметры шаблонов', '/admin/admin/content_template_param/', 'content_template_param', '', '0')");
    }
};