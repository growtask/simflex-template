<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Admin\AdminModules;
use Simflex\Admin\Menu;
use Simflex\Core\CodeRegistry;
use Simflex\Core\Container;
use Simflex\Core\Privilege;
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
