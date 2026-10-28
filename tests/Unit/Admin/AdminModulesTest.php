<?php

namespace Tests\Unit\Admin;

use Codeception\Test\Unit;
use Simflex\Core\Admin\AdminModule;
use Simflex\Core\Admin\AdminModules;
use Simflex\Core\Admin\Modules\Breadcrumbs\Breadcrumbs;
use Simflex\Core\Admin\Modules\Menu\Menu as MenuModule;
use Simflex\Core\System\Container;
use Simflex\Core\Extensions\Block\Block;
use Tests\Support\MockDb;

/**
 * Модули макета админки, объявленные в коде (\Simflex\Core\Admin\AdminModules)
 */
class AdminModulesTest extends Unit
{
    use MockDb;

    protected function _before(): void
    {
        AdminModules::reset();
    }

    protected function _after(): void
    {
        AdminModules::reset();
    }

    public function testCoreModulesOnEveryPage(): void
    {
        $this->assertSame(AdminModules::class, AdminModules::registryClass());
        foreach (['', '/content', '/site/seo'] as $section) {
            $this->assertSame(
                [MenuModule::class => 'menu', Breadcrumbs::class => 'breadcrumbs'],
                $this->positions(AdminModules::getForSection($section))
            );
        }
    }

    public function testAppModulesOfSections(): void
    {
        Container::getConfig()->adminModulesClass = TestAdminModules::class;

        $this->assertSame([MenuModule::class => 'menu'], $this->positions(AdminModules::getForSection('/site')));
        $this->assertSame(
            [MenuModule::class => 'menu', Block::class => 'help'],
            $this->positions(AdminModules::getForSection('/content'))
        );
        $this->assertSame(['text' => 'Справка'], AdminModules::getForSection('/content')[1]->params);
    }

    /**
     * @param AdminModule[] $modules
     */
    private function positions(array $modules): array
    {
        $out = [];
        foreach ($modules as $module) {
            $out[$module->class] = $module->position;
        }
        return $out;
    }
}

class TestAdminModules extends AdminModules
{
    protected static function init(): void
    {
        parent::init();
        static::remove(Breadcrumbs::class);
        static::add(new AdminModule(Block::class, 'help', 'Справка', ['text' => 'Справка'], sections: ['/content']));
    }
}
