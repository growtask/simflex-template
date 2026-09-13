<?php

use Simflex\Admin\Fields\FieldText;
use Simflex\Core\Models\Module;
use Simflex\Core\Models\ModuleItem;
use Simflex\Core\Models\ModuleParam;

return new class implements \Simflex\Core\DB\Seeder {
    public function seed(): void
    {
        $adminMenu = Module::insertStatic([
            'class' => 'Menu',
            'name' => 'Admin - Меню',
            'type' => 'admin',
            'postexec' => 0,
        ]);

        ModuleItem::bulkInsert([
            [
                'module_id' => $adminMenu->module_id,
                'menu_id' => null,
                'posname' => 'menu',
                'active' => 1,
                'npp' => 1,
                'name' => 'Admin - Меню',
                'params' => ''
            ],
        ]);

        $adminBc = Module::insertStatic([
            'class' => 'Breadcrumbs',
            'name' => 'Admin - Хлебные крошки',
            'type' => 'admin',
            'postexec' => 0,
        ]);

        ModuleItem::bulkInsert([
            [
                'module_id' => $adminBc->module_id,
                'menu_id' => null,
                'posname' => 'breadcrumbs',
                'active' => 1,
                'npp' => 2,
                'name' => 'Admin - Хлебные крошки',
                'params' => ''
            ],
        ]);

        $codeModule = Module::insertStatic([
            'class' => '\Simflex\Extensions\Code\Code',
            'name' => 'Код',
            'type' => 'site',
        ]);
        $leftPosition = ModuleParam::insertStatic([
            'module_id' => $codeModule->module_id,
            'position' => 'left',
            'name' => 'module_param_content',
            'label' => 'Контент',
            'params' => 'a:1:{s:17:"module_param_main";a:1:{s:13:"default_value";s:0:"";}}',
        ]);
        ModuleParam::insertStatic([
            'module_id' => $codeModule->module_id,
            'param_pid' => $leftPosition->getId(),
            'field_type' => FieldText::class,
            'name' => 'content',
            'label' => 'Текст',
            'params' => 'a:2:{s:17:"module_param_main";a:1:{s:13:"default_value";s:0:"";}s:4:"main";a:2:{s:11:"editor_mini";s:1:"0";s:11:"editor_full";s:1:"0";}}',
        ]);

        $blockModule = Module::insertStatic([
            'class' => '\Simflex\Extensions\Block\Block',
            'name' => 'Текстовый блок',
            'type' => 'site',
        ]);
        $leftPosition = ModuleParam::insertStatic([
            'module_id' => $blockModule->module_id,
            'position' => 'left',
            'name' => 'module_param_content',
            'label' => 'Контент',
            'params' => 'a:1:{s:17:"module_param_main";a:1:{s:13:"default_value";s:0:"";}}',
        ]);
        ModuleParam::insertStatic([
            'module_id' => $codeModule->module_id,
            'param_pid' => $leftPosition->getId(),
            'field_type' => FieldText::class,
            'name' => 'content',
            'label' => 'Текст',
            'params' => 'a:2:{s:17:"module_param_main";a:1:{s:13:"default_value";s:0:"";}s:4:"main";a:2:{s:11:"editor_mini";s:1:"0";s:11:"editor_full";s:1:"1";}}',
        ]);
    }
};
