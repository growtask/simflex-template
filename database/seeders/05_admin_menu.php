<?php

return new class implements \Simflex\Core\DB\Seeder
{
    public function seed(): void
    {
        $devPriv = \Simflex\Core\Models\UserPriv::byName('dev');
        $adminPriv = \Simflex\Core\Models\UserPriv::byName('admin');

        $adminMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => null,
            'priv_id' => $devPriv->priv_id,
            'npp' => 1,
            'name' => 'Управление',
            'link' => '/admin/admin/',
            'model' => '',
            'icon' => 'home',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 1,
                'name' => 'Меню',
                'link' => '/admin/admin/menu/',
                'model' => 'admin_menu',
                'icon' => 'settings-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 3,
                'name' => 'Роли',
                'link' => '/admin/admin/role/',
                'model' => 'user_role',
                'icon' => 'users-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 6,
                'name' => 'Привилегии',
                'link' => '/admin/admin/priv/',
                'model' => 'user_priv',
                'icon' => 'fingerprint-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 2,
                'name' => 'Пользователи',
                'link' => '/admin/admin/user/',
                'model' => 'user',
                'icon' => 'user-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $adminMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 7,
                'name' => 'Cron',
                'link' => '/admin/cron/',
                'model' => 'cron',
                'icon' => 'calendar-mini',
                'hidden' => false
            ]
        ]);

        $siteMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => null,
            'priv_id' => $adminPriv->priv_id,
            'npp' => 2,
            'name' => 'Настройки сайта',
            'link' => '/admin/site/',
            'model' => '',
            'icon' => 'settings',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 1,
                'name' => 'Меню',
                'link' => '/admin/site/menu/',
                'model' => 'menu',
                'icon' => 'user-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 2,
                'name' => 'Модули',
                'link' => '/admin/site/module/',
                'model' => 'module_item',
                'icon' => 'puzzle-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 8,
                'name' => 'Настройки',
                'link' => '/admin/site/settings/',
                'model' => 'settings',
                'icon' => 'settings-mini',
                'hidden' => false
            ],
            [
                'menu_pid' => $siteMenu->menu_id,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 9,
                'name' => 'SEO',
                'link' => '/admin/site/seo/',
                'model' => 'seo',
                'icon' => 'chart-mini',
                'hidden' => false
            ]
        ]);

        $structMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 2,
            'name' => 'Структура',
            'link' => '/admin/admin/structure/',
            'model' => 'struct_data',
            'icon' => 'layout-mini',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $structMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 2,
                'name' => 'Типы полей',
                'link' => '/admin/admin/structure/fields/',
                'model' => 'struct_field',
                'icon' => 'puzzle',
                'hidden' => false
            ],
            [
                'menu_pid' => $structMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 1,
                'name' => 'Таблицы БД',
                'link' => '/admin/admin/structure/tables/',
                'model' => 'struct_table',
                'icon' => 'puzzle',
                'hidden' => false
            ],
            [
                'menu_pid' => $structMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 3,
                'name' => 'Параметры таблиц',
                'link' => '/admin/admin/structure/params/',
                'model' => 'struct_param',
                'icon' => 'puzzle',
                'hidden' => false
            ],
            [
                'menu_pid' => $structMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 5,
                'name' => 'Поля в таблицах',
                'link' => '/admin/admin/structure/',
                'model' => 'struct_data',
                'icon' => 'puzzle',
                'hidden' => false
            ],
            [
                'menu_pid' => $structMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 4,
                'name' => 'Параметры полей',
                'link' => '/admin/admin/structure/field_param/',
                'model' => 'struct_field_param',
                'icon' => 'puzzle',
                'hidden' => false
            ]
        ]);

        $modulesMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 3,
            'name' => 'Модули',
            'link' => '/admin/admin/module/',
            'model' => 'module',
            'icon' => 'puzzle-mini',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $modulesMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 5,
                'name' => 'Параметры модулей',
                'link' => '/admin/admin/module/param/',
                'model' => 'module_param',
                'icon' => '',
                'hidden' => false
            ]
        ]);

        $componentsMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 4,
            'name' => 'Компоненты',
            'link' => '/admin/admin/component/',
            'model' => 'component',
            'icon' => 'container-mini',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $componentsMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 4,
                'name' => 'Параметры компонентов',
                'link' => '/admin/admin/component_param/',
                'model' => 'component_param',
                'icon' => '',
                'hidden' => false
            ]
        ]);

        $permissionsMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 6,
            'name' => 'Привилегии',
            'link' => '/admin/admin/priv/',
            'model' => 'user_priv',
            'icon' => 'fingerprint-mini',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $permissionsMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 1,
                'name' => 'Персональные права',
                'link' => '/admin/user_priv_personal/',
                'model' => 'user_priv_personal',
                'icon' => '',
                'hidden' => false
            ]
        ]);

        $templatesMenu = \Simflex\Core\Models\AdminMenu::insertStatic([
            'menu_pid' => $adminMenu->menu_id,
            'priv_id' => $devPriv->priv_id,
            'npp' => 7,
            'name' => 'Шаблоны страниц',
            'link' => '/admin/admin/content_template/',
            'model' => 'content_template',
            'icon' => 'layout-mini',
            'hidden' => false
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => $templatesMenu->menu_id,
                'priv_id' => $devPriv->priv_id,
                'npp' => 1,
                'name' => 'Параметры шаблонов',
                'link' => '/admin/admin/content_template_param/',
                'model' => 'content_template_param',
                'icon' => '',
                'hidden' => false
            ]
        ]);

        \Simflex\Core\Models\AdminMenu::bulkInsert([
            [
                'menu_pid' => null,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 3,
                'name' => 'Редактор страниц',
                'link' => '/admin/content/',
                'model' => 'content',
                'icon' => 'edit',
                'hidden' => false
            ],
            [
                'menu_pid' => null,
                'priv_id' => $adminPriv->priv_id,
                'npp' => 0,
                'name' => 'Аккаунт',
                'link' => '/admin/account/',
                'model' => '',
                'icon' => 'user',
                'hidden' => true
            ],
        ]);
    }
};