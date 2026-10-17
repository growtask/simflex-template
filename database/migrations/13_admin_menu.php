<?php

use Simflex\Core\DB\Schema;
use App\Core\Privilege;

return new class () implements \Simflex\Core\DB\Migration {
    public function up(Schema $s): void
    {
        $s->createTable('admin_menu', function (Schema\Table $c) {
            $c->id('menu_id');
            $c->integer('menu_pid');
            $c->string('privilege', 64);
            $c->integer('npp')->setDefault(0);
            $c->string('name');
            $c->string('link');
            $c->string('model');
            $c->string('icon');
            $c->boolean('hidden')->setDefault(0);
        });
        // todo: add keys post-factum

        $s->seed(function () {
            $adminMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
                'menu_pid' => null,
                'privilege' => Privilege::DEV,
                'npp' => 1,
                'name' => 'Управление',
                'link' => '/admin',
                'model' => '',
                'icon' => 'home',
                'hidden' => false,
            ]);

            \Simflex\Core\Models\AdminMenu::bulkInsert([
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 1,
                    'name' => 'Меню',
                    'link' => '/admin/menu',
                    'model' => 'admin_menu',
                    'icon' => 'container-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 3,
                    'name' => 'Роли',
                    'link' => '/admin/role',
                    'model' => 'user_role',
                    'icon' => 'users-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 2,
                    'name' => 'Привилегии',
                    'link' => '/admin/privilege',
                    'model' => 'user_privilege',
                    'icon' => 'lock-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 5,
                    'name' => 'Пользователи',
                    'link' => '/admin/user',
                    'model' => 'user',
                    'icon' => 'user-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 7,
                    'name' => 'Cron',
                    'link' => '/cron',
                    'model' => 'cron',
                    'icon' => 'calendar-mini',
                    'hidden' => false,
                ],
            ]);

            $siteMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
                'menu_pid' => null,
                'privilege' => Privilege::ADMIN,
                'npp' => 2,
                'name' => 'Настройки сайта',
                'link' => '/site',
                'model' => '',
                'icon' => 'settings',
                'hidden' => false,
            ]);

            \Simflex\Core\Models\AdminMenu::bulkInsert([
                [
                    'menu_pid' => $siteMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 1,
                    'name' => 'Меню',
                    'link' => '/site/menu',
                    'model' => 'menu',
                    'icon' => 'container-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $siteMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 2,
                    'name' => 'Модули',
                    'link' => '/site/module',
                    'model' => 'module_item',
                    'icon' => 'puzzle-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $siteMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 8,
                    'name' => 'Настройки',
                    'link' => '/site/settings',
                    'model' => 'settings',
                    'icon' => 'settings-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $siteMenu->menu_id,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 9,
                    'name' => 'SEO',
                    'link' => '/site/seo',
                    'model' => 'seo',
                    'icon' => 'chart-mini',
                    'hidden' => false,
                ],
            ]);

            \Simflex\Core\Models\AdminMenu::bulkInsert([
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::DEV,
                    'npp' => 4,
                    'name' => 'Привилегии ролей',
                    'link' => '/admin/role_privilege',
                    'model' => 'user_role_privilege',
                    'icon' => 'file-shield-mini',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => $adminMenu->menu_id,
                    'privilege' => Privilege::DEV,
                    'npp' => 6,
                    'name' => 'Персональные привилегии',
                    'link' => '/admin/privilege_personal',
                    'model' => 'user_privilege_personal',
                    'icon' => 'fingerprint-mini',
                    'hidden' => false,
                ],
            ]);

            \Simflex\Core\Models\AdminMenu::bulkInsert([
                [
                    'menu_pid' => null,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 3,
                    'name' => 'Редактор страниц',
                    'link' => '/content',
                    'model' => 'content',
                    'icon' => 'edit',
                    'hidden' => false,
                ],
                [
                    'menu_pid' => null,
                    'privilege' => Privilege::ADMIN,
                    'npp' => 0,
                    'name' => 'Аккаунт',
                    'link' => '/account',
                    'model' => '',
                    'icon' => 'user',
                    'hidden' => true,
                ],
            ]);
        });
    }

    public function down(Schema $s): void
    {
        $s->dropTable('admin_menu');
    }
};
