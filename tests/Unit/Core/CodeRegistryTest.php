<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Admin\AdminModules;
use Simflex\Core\Admin\Menu;
use Simflex\Core\System\CodeRegistry;
use Simflex\Core\System\Container;
use Simflex\Core\System\Privilege;
use Tests\Support\MockDb;

/**
 * Реестры, объявленные в коде (Privilege, Admin\Menu, Admin\AdminModules): подкласс сайта из конфига
 */
class CodeRegistryTest extends Unit
{
    use MockDb;

    public function testAppSubclassFromConfig(): void
    {
        $config = Container::getConfig();
        $config->privilegeClass = \App\Core\Privilege::class;
        $config->adminMenuClass = \App\Admin\Menu::class;

        $this->assertSame(\App\Core\Privilege::class, Privilege::registryClass());
        $this->assertSame(\App\Admin\Menu::class, Menu::registryClass());
        $this->assertSame(AdminModules::class, AdminModules::registryClass());
        $this->assertTrue(is_subclass_of(Menu::class, CodeRegistry::class));
    }

    public function testNotSubclassIgnored(): void
    {
        // класс другого реестра или отсутствующий: используется реестр ядра
        Container::getConfig()->adminMenuClass = \App\Core\Privilege::class;
        Container::getConfig()->adminModulesClass = 'App\Admin\NoSuchModules';

        $this->assertSame(Menu::class, Menu::registryClass());
        $this->assertSame(AdminModules::class, AdminModules::registryClass());
    }
}
