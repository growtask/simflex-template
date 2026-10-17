<?php

use App\Core\Privilege;

return new class () implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
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
                'icon' => 'settings-mini',
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
                'npp' => 6,
                'name' => 'Привилегии',
                'link' => '/admin/privilege',
                'model' => 'user_privilege',
                'icon' => 'fingerprint-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'privilege' => Privilege::ADMIN,
                'npp' => 2,
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
                'icon' => 'user-mini',
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
                'npp' => 6,
                'name' => 'Привилегии ролей',
                'link' => '/admin/role_privilege',
                'model' => 'user_role_privilege',
                'icon' => 'fingerprint-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'privilege' => Privilege::DEV,
                'npp' => 6,
                'name' => 'Персональные права',
                'link' => '/admin/privilege_personal',
                'model' => 'user_privilege_personal',
                'icon' => 'fingerprint-mini',
                'hidden' => false,
            ],
        ]);

        $templatesMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'privilege' => Privilege::DEV,
            'npp' => 7,
            'name' => 'Шаблоны страниц',
            'link' => '/admin/templates',
            'model' => '',
            'icon' => 'layout-mini',
            'hidden' => false,
        ]);

        // a menu item having subitems only opens them: the section itself is the first subitem
        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $templatesMenu->menu_id,
                'privilege' => Privilege::DEV,
                'npp' => 0,
                'name' => 'Шаблоны страниц',
                'link' => '/admin/content_template',
                'model' => 'content_template',
                'icon' => '',
                'hidden' => false,
            ],
            [
                'menu_pid' => $templatesMenu->menu_id,
                'privilege' => Privilege::DEV,
                'npp' => 1,
                'name' => 'Параметры шаблонов',
                'link' => '/admin/content_template_param',
                'model' => 'content_template_param',
                'icon' => '',
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
    }
};
