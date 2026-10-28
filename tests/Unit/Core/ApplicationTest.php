<?php

namespace Tests\Unit\Core;

use Simflex\Application;
use Codeception\Test\Unit;
use Simflex\Core\SiteSettings;

class ApplicationTest extends Unit
{
    private ?array $siteParams = null;

    protected function _before(): void
    {
        $this->siteParams = (new \ReflectionProperty(SiteSettings::class, 'values'))->getValue();
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(SiteSettings::class, 'values'))->setValue(null, $this->siteParams);
    }

    public function testPrepareOutputReplacesSiteParams(): void
    {
        (new \ReflectionProperty(SiteSettings::class, 'values'))->setValue(null, ['phone' => '123', 'empty' => null]);
        $sf = (new \ReflectionClass(Application::class))->newInstanceWithoutConstructor();
        require_once SF_ROOT_PATH . '/config.php';
        $config = (new \ReflectionClass(\App\Config::class))->newInstanceWithoutConstructor();
        $config->devMode = true;
        (new \ReflectionProperty(Application::class, 'cfg'))->setValue($sf, $config);

        $this->assertSame('call 123 () ' . date('Y'), $sf->prepareOutput('call {phone} ({empty}) {year}'));
    }
}
