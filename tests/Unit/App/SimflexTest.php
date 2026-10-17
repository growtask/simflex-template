<?php

namespace Tests\Unit\App;

use App\Core\Simflex;
use Codeception\Test\Unit;
use Simflex\Core\Core;

class SimflexTest extends Unit
{
    private array $siteParams = [];

    protected function _before(): void
    {
        $this->siteParams = (new \ReflectionProperty(Core::class, 'siteParams'))->getValue();
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(Core::class, 'siteParams'))->setValue(null, $this->siteParams);
    }

    public function testPrepareOutputReplacesSiteParams(): void
    {
        (new \ReflectionProperty(Core::class, 'siteParams'))->setValue(null, ['phone' => '123', 'empty' => null]);
        $sf = (new \ReflectionClass(Simflex::class))->newInstanceWithoutConstructor();
        require_once SF_ROOT_PATH . '/config.php';
        $config = (new \ReflectionClass(\App\Config::class))->newInstanceWithoutConstructor();
        $config->devMode = true;
        (new \ReflectionProperty(Simflex::class, 'cfg'))->setValue($sf, $config);

        $this->assertSame('call 123 () ' . date('Y'), $sf->prepareOutput('call {phone} ({empty}) {year}'));
    }
}
