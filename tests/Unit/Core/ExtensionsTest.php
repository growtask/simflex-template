<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Admin\Menu;
use Simflex\Admin\MenuItem;
use Simflex\Admin\Table\FieldDefinition;
use Simflex\Admin\Table\Presets\IdField;
use Simflex\Admin\Table\Repository;
use Simflex\Admin\Table\Table;
use Simflex\Http\ComponentBase;
use Simflex\Cron\CronJob;
use Simflex\Cron\CronRegistry;
use Simflex\Core\ExtensionConfig;
use Simflex\Core\ExtensionRegistry;
use Simflex\Core\ModelBase;
use Simflex\Core\ModuleBase;
use Simflex\Core\Privilege;
use Simflex\Core\PrivilegeParams;
use Tests\Support\MockDb;

/**
 * Реестры расширений: компоненты, модули, разделы, задачи cron, привилегии и пункты меню из ExtensionConfig
 * объединяются с объявлениями ядра и сайта
 */
class ExtensionsTest extends Unit
{
    use MockDb;

    protected function _before(): void
    {
        $this->resetRegistries();
        ExtensionConfig::reset([new TestExtension()]);
    }

    protected function _after(): void
    {
        ExtensionConfig::reset();
        $this->resetRegistries();
    }

    public function testComponentsAndModules(): void
    {
        $this->assertArrayHasKey(TestExtensionComponent::class, ExtensionRegistry::components());
        // компоненты сайта (provider/components.php) остаются
        $this->assertArrayHasKey(\App\Extensions\Content\Content::class, ExtensionRegistry::components());
        $this->assertArrayHasKey(TestExtensionModule::class, ExtensionRegistry::modules());
    }

    public function testAppSubclassReplacesExtensionClass(): void
    {
        $this->assertSame(
            ['Other', TestAppComponent::class],
            ExtensionConfig::mergeClasses([TestExtensionComponent::class, 'Other'], [TestAppComponent::class])
        );
    }

    public function testHandlersAndTables(): void
    {
        $this->assertContains(TestExtensionTable::class, Repository::getHandlerClasses());
        $this->assertSame(TestExtensionTable::class, Repository::getTableClass('test_extension'));
        // разделы ядра остаются
        $this->assertSame(\Simflex\Admin\Handlers\Seo::class, Repository::getTableClass('seo'));
    }

    public function testCronJobs(): void
    {
        $this->assertSame('Задача расширения', CronRegistry::job('test_extension_job')?->name);
        // задачи сайта (provider/cron.php) остаются
        $this->assertNotNull(CronRegistry::job('cron_log_clean'));
    }

    public function testPrivilegesAndMenu(): void
    {
        $this->assertSame('Привилегия расширения', Privilege::getParams('test_extension')?->title);
        $this->assertNotNull(Privilege::getParams(Privilege::ADMIN));

        $items = Menu::getItems();
        $this->assertSame('Расширение', $items['/test-extension']->name);
        $this->assertSame('/test-extension', Menu::getParent('/test-extension/rows'));
        // пункты ядра на месте, пункт расширения — после них
        $this->assertSame('/content', array_keys($items)[array_search('/test-extension', array_keys($items)) - 1]);
    }

    public function testRoutesServicesEventsAndCommands(): void
    {
        // в unit-тестах конфиг контейнера подменён, берём конфиг сайта
        require_once SF_ROOT_PATH . '/config.php';
        $config = new \App\Config();
        $config->load();

        // маршрут и команда расширения добавляются, маршрут и команда сайта с тем же именем главнее,
        // команда расширения главнее команды ядра
        $routes = $config->getRoutes();
        $this->assertSame(TestExtensionComponent::class, $routes['/test-extension']);
        $this->assertSame(\App\Extensions\Content\Content::class, $routes['/']);
        $this->assertSame(\App\Extensions\Install\Installer::class, $config->getCommands()['install']);
        $this->assertSame(TestExtensionComponent::class, $config->getCommands()['migrate']);
        $this->assertSame(\Simflex\Layout\ConsoleLayout::class, $config->getCommands()['layout']);
        $this->assertSame(TestExtensionComponent::class, $config->getCommands()['test-extension']);

        // сервисы окружения объединяются, сервисы сайта — после сервисов расширения (заменяют их в контейнере)
        $services = $config->getServices();
        $this->assertSame(TestExtensionComponent::class, $services['web'][0]);
        $this->assertContains(\Simflex\Http\Html\HtmlRequest::class, $services['web']);

        // слушатели одного события из расширения и сайта складываются
        $this->assertSame([TestExtensionComponent::class], $config->getEvents()['test-event']);
    }

    public function testLoadOrder(): void
    {
        $first = new TestExtension();
        $first->name = 'first';
        $first->loadOrder = -1;
        ExtensionConfig::reset([new TestExtension(), $first]);
        $this->assertSame(['first', 'test'], array_map(fn(ExtensionConfig $config) => $config->name, ExtensionConfig::getAll()));
    }

    private function resetRegistries(): void
    {
        ExtensionRegistry::reset();
        CronRegistry::reset();
        Privilege::reset();
        Menu::reset();
    }
}

class TestExtension extends ExtensionConfig
{
    public string $name = 'test';
    public int $loadOrder = 0;
    protected array $components = [TestExtensionComponent::class];
    protected array $modules = [TestExtensionModule::class];
    protected array $handlers = [TestExtensionTable::class];
    protected array $routes = ['/test-extension' => TestExtensionComponent::class, '/' => TestExtensionComponent::class];
    protected array $services = ['web' => [TestExtensionComponent::class]];
    protected array $events = ['test-event' => [TestExtensionComponent::class]];
    protected array $commands = ['test-extension' => TestExtensionComponent::class, 'migrate' => TestExtensionComponent::class, 'install' => TestExtensionComponent::class];

    public function getCronJobs(): array
    {
        return ['test_extension_job' => new CronJob(name: 'Задача расширения', handler: [\Simflex\Models\CronLog::class, 'deleteOld'], timing: '* * * * *')];
    }

    public function getPrivileges(): array
    {
        return ['test_extension' => new PrivilegeParams('Привилегия расширения')];
    }

    public function getAdminMenu(): array
    {
        return [
            new MenuItem('Расширение', '/test-extension', children: [
                new MenuItem('Записи', '/test-extension/rows', handler: TestExtensionTable::class),
            ]),
        ];
    }
}

class TestExtensionComponent extends ComponentBase
{
    protected function content(): string
    {
        return '';
    }
}

class TestAppComponent extends TestExtensionComponent
{
}

class TestExtensionModule extends ModuleBase
{
    protected function content(): string
    {
        return '';
    }
}

class TestExtensionModel extends ModelBase
{
    protected static string $table = 'test_extension';
    protected static string $primaryKeyName = 'id';
}

class TestExtensionTable extends Table
{
    protected static string $model = TestExtensionModel::class;

    public static function getFields(): array
    {
        return [new IdField(), new FieldDefinition(name: 'name', class: \Simflex\Admin\Fields\FieldString::class)];
    }
}
