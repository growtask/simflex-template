<?php

return new class () implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        $devPriv = \Simflex\Core\Models\UserPriv::byName('dev');
        $adminPriv = \Simflex\Core\Models\UserPriv::byName('admin');

        $adminMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => null,
            'priv_id' => $devPriv->priv_id,
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
                'priv_id' => $adminPriv->priv_id,
                'npp' => 1,
                'name' => 'Меню',
                'link' => '/admin/menu',
                'model' => 'admin_menu',
                'icon' => 'settings-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 3,
                'name' => 'Роли',
                'link' => '/admin/role',
                'model' => 'user_role',
                'icon' => 'users-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 6,
                'name' => 'Привилегии',
                'link' => '/admin/priv',
                'model' => 'user_priv',
                'icon' => 'fingerprint-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 2,
                'name' => 'Пользователи',
                'link' => '/admin/user',
                'model' => 'user',
                'icon' => 'user-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
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
            'priv_id' => $adminPriv->priv_id,
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
                'priv_id' => $adminPriv->priv_id,
                'npp' => 1,
                'name' => 'Меню',
                'link' => '/site/menu',
                'model' => 'menu',
                'icon' => 'user-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 2,
                'name' => 'Модули',
                'link' => '/site/module',
                'model' => 'module_item',
                'icon' => 'puzzle-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 8,
                'name' => 'Настройки',
                'link' => '/site/settings',
                'model' => 'settings',
                'icon' => 'settings-mini',
                'hidden' => false,
            ],
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 9,
                'name' => 'SEO',
                'link' => '/site/seo',
                'model' => 'seo',
                'icon' => 'chart-mini',
                'hidden' => false,
            ],
        ]);

        $permissionsMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 6,
            'name' => 'Привилегии',
            'link' => '/admin/priv',
            'model' => 'user_priv',
            'icon' => 'fingerprint-mini',
            'hidden' => false,
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $permissionsMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 1,
                'name' => 'Персональные права',
                'link' => '/user_priv_personal',
                'model' => 'user_priv_personal',
                'icon' => '',
                'hidden' => false,
            ],
        ]);

        $templatesMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 7,
            'name' => 'Шаблоны страниц',
            'link' => '/admin/content_template',
            'model' => 'content_template',
            'icon' => 'layout-mini',
            'hidden' => false,
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $templatesMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
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
                'priv_id' => $adminPriv->priv_id,
                'npp' => 3,
                'name' => 'Редактор страниц',
                'link' => '/content',
                'model' => 'content',
                'icon' => 'edit',
                'hidden' => false,
            ],
            [
                'menu_pid' => null,
                'priv_id' => $adminPriv->priv_id,
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
