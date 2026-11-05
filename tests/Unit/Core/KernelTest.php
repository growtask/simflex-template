<?php

namespace Tests\Unit\Core;

use Simflex\Core\System\Container;
use Simflex\Core\Kernel;
use Codeception\Test\Unit;
use Simflex\Core\System\Settings;
use Simflex\Core\System\Privilege;
use Simflex\Core\Models\User;

class KernelTest extends Unit
{
    private ?array $siteParams = null;

    protected function _before(): void
    {
        $this->siteParams = (new \ReflectionProperty(Settings::class, 'values'))->getValue();
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(Settings::class, 'values'))->setValue(null, $this->siteParams);
    }

    public function testPrepareOutputReplacesSiteParams(): void
    {
        (new \ReflectionProperty(Settings::class, 'values'))->setValue(null, ['phone' => '123', 'empty' => null]);
        $sf = (new \ReflectionClass(Kernel::class))->newInstanceWithoutConstructor();
        require_once Container::getPaths()->getRoot('config.php');
        $config = (new \ReflectionClass(\App\Config::class))->newInstanceWithoutConstructor();
        $config->devMode = true;
        (new \ReflectionProperty(Kernel::class, 'cfg'))->setValue($sf, $config);

        $this->assertSame('call 123 () ' . date('Y'), $sf->prepareOutput('call {phone} ({empty}) {year}'));
    }

    /**
     * Файловый менеджер: пользователь админки с привилегией из конфига fileManagerPrivilege
     */
    public function testFileManagerAccessUsesConfigPrivilege(): void
    {
        $oldConfig = Container::get('config');
        $oldUser = Container::get('user');
        require_once Container::getPaths()->getRoot('config.php');
        $config = (new \ReflectionClass(\App\Config::class))->newInstanceWithoutConstructor();
        Container::set('config', $config);
        $kernel = (new \ReflectionClass(Kernel::class))->newInstanceWithoutConstructor();
        try {
            Container::set('user', null);
            $this->assertFalse($kernel->canUseFileManager());

            Container::set('user', $this->userWithPrivileges([Privilege::ADMIN]));
            $this->assertSame(Privilege::ADMIN, $config->fileManagerPrivilege);
            $this->assertTrue($kernel->canUseFileManager());

            $config->fileManagerPrivilege = 'file_manager';
            $this->assertFalse($kernel->canUseFileManager());
            Container::set('user', $this->userWithPrivileges([Privilege::ADMIN, 'file_manager']));
            $this->assertTrue($kernel->canUseFileManager());
            // привилегия файлового менеджера без входа в админку не даёт доступа
            Container::set('user', $this->userWithPrivileges(['file_manager']));
            $this->assertFalse($kernel->canUseFileManager());
        } finally {
            Container::set('config', $oldConfig);
            Container::set('user', $oldUser);
        }
    }

    /**
     * @param list<string> $privileges
     */
    private function userWithPrivileges(array $privileges): User
    {
        return new class ($privileges) extends User {
            /** @param list<string> $names */
            public function __construct(private array $names)
            {
            }

            public function getPrivileges(): array
            {
                return array_combine($this->names, $this->names);
            }
        };
    }

    /**
     * Вне devMode необработанное исключение пишется в журнал PHP независимо от enableLogging
     * и не остаётся пустым ответом 200
     */
    public function testUncaughtExceptionIsWrittenToPhpLog(): void
    {
        $kernel = (new \ReflectionClass(Kernel::class))->newInstanceWithoutConstructor();
        $file = tempnam(sys_get_temp_dir(), 'sf-errlog');
        $oldLog = ini_set('error_log', $file);
        $oldStatus = http_response_code();
        try {
            ob_start();
            $kernel->handleException(new \RuntimeException('Duplicate entry for key email_uq'));
            $output = ob_get_clean();
            $log = (string)file_get_contents($file);
        } finally {
            ini_set('error_log', $oldLog === false ? '' : $oldLog);
            @unlink($file);
            $oldStatus ? http_response_code($oldStatus) : null;
        }
        $this->assertStringContainsString('RuntimeException', $log);
        $this->assertStringContainsString('Duplicate entry for key email_uq', $log);
        $this->assertStringContainsString('KernelTest.php', $log);
        $this->assertStringContainsString('Stack trace', $log);
        // в CLI тело не печатается, в вебе — короткий текст без деталей
        $this->assertStringNotContainsString('Duplicate', $output);
    }
}
