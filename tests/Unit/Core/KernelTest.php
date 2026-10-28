<?php

namespace Tests\Unit\Core;

use Simflex\Core\System\Container;
use Simflex\Core\Kernel;
use Codeception\Test\Unit;
use Simflex\Core\System\Settings;

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
}
