<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Admin\Fields\FieldBool;
use Simflex\Admin\Fields\FieldString;
use Simflex\Core\ComponentBase;
use Simflex\Core\Container;
use Simflex\Core\Html\HtmlRequest;
use Simflex\Core\ModuleBase;
use Simflex\Core\Page;
use Simflex\Core\View;

/**
 * Components, modules, fields and pages return their HTML, output happens only in the system layer
 */
class OutputReturnTest extends Unit
{
    private string $tpl = '';

    protected function _after(): void
    {
        if ($this->tpl && is_file($this->tpl)) {
            unlink($this->tpl);
        }
    }

    private function tpl(string $code): string
    {
        $this->tpl = tempnam(sys_get_temp_dir(), 'tpl');
        file_put_contents($this->tpl, $code);
        return $this->tpl;
    }

    public function testViewRendersVarsAndContext(): void
    {
        $file = $this->tpl('<?= $a ?>-<?= $this->secret() ?>');
        $context = new class () {
            private function secret(): string
            {
                return 's';
            }
        };
        $this->expectOutputString('');
        $this->assertSame('1-s', View::render($file, ['a' => 1], $context));
    }

    public function testViewCleansBufferOnException(): void
    {
        $file = $this->tpl('partial<?php throw new \RuntimeException("tpl"); ?>');
        $level = ob_get_level();
        try {
            View::render($file);
            $this->fail('Exception expected');
        } catch (\RuntimeException $e) {
            $this->assertSame('tpl', $e->getMessage());
        }
        $this->assertSame($level, ob_get_level());
    }

    public function testComponentExecuteReturnsContent(): void
    {
        $component = new class () extends ComponentBase {
            protected function content(): string
            {
                return 'component-body';
            }
        };
        $this->expectOutputString('');
        $this->assertSame('component-body', $component->execute());
    }

    public function testModuleExecuteWrapsContent(): void
    {
        $row = ['item_id' => 5, 'class' => 'SF_Test', 'name' => 'Title', 'posname' => 'left'];
        $make = fn(array $params, string $body) => new class ($row + ['params' => serialize($params)], $body) extends ModuleBase {
            public function __construct($module, private string $body)
            {
                parent::__construct($module);
            }

            protected function content(): string
            {
                return $this->body;
            }
        };

        $hadRequest = Container::isSet('request');
        $oldRequest = $hadRequest ? Container::get('request') : null;
        Container::set('request', new HtmlRequest());
        try {
            $this->expectOutputString('');
            $this->assertSame('', $make(['is_wrap' => 1, 'is_title' => 1], '')->execute());
            $this->assertSame('<h2>Title</h2>body', $make(['is_title' => 1], 'body')->execute());
            $this->assertSame(
                '<div id="module-5" class="module-base module-test"><div class="module-body">body</div></div>',
                $make(['is_wrap' => 1], 'body')->execute()
            );
        } finally {
            Container::set('request', $oldRequest);
        }
    }

    public function testPagePositionJoinsModules(): void
    {
        $prop = new \ReflectionProperty(Page::class, 'positions');
        $backup = $prop->getValue();
        $prop->setValue(null, ['left' => ['a', 'b']]);
        try {
            $this->expectOutputString('');
            $this->assertSame('a<hr/>b', Page::position('left', '<hr/>'));
            $this->assertSame('', Page::position('right'));
        } finally {
            $prop->setValue(null, $backup);
        }
    }

    public function testAdminFieldsReturnCellsAndFilters(): void
    {
        $string = new FieldString(['name' => 'title', 'label' => 'Title', 'table' => 't']);
        $string->filter = true;
        $bool = new FieldBool(['name' => 'active', 'label' => 'Active', 'table' => 't']);

        $this->expectOutputString('');
        $this->assertStringContainsString('Hello', $string->show(['title' => 'Hello']));
        $this->assertStringContainsString('filter[title]', $string->filter('x'));
        $this->assertNotSame('', $bool->show(['active' => 1]));
        $this->assertSame('', $bool->filter('1'));
    }

}
