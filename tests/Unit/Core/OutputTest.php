<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Http\ComponentBase;
use Simflex\Core\System\Container;
use Simflex\Core\Http\Html\HtmlRequest;
use Simflex\Core\System\ModuleBase;
use Simflex\Core\Models\ModuleItem;
use Simflex\Core\Http\Page;
use Simflex\Core\Http\View;

/**
 * Представления, компоненты, модули и страницы возвращают свой HTML, вывод происходит только на системном уровне
 */
class OutputTest extends Unit
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

    public function testModuleItemParamsOnBrokenData(): void
    {
        $item = new ModuleItem();
        $item->params = 'not serialized';
        $this->assertSame([], $item->getParams());
        $item->params = '';
        $this->assertSame([], $item->getParams());
        $item->setParams(['a' => 1]);
        $this->assertSame(['a' => 1], $item->getParams());
    }

    public function testPagePositionJoinsModules(): void
    {
        $page = new Page();
        new \ReflectionProperty(Page::class, 'positions')->setValue($page, ['left' => ['a', 'b']]);
        $this->expectOutputString('');
        $this->assertSame('a<hr/>b', $page->getPosition('left', '<hr/>'));
        $this->assertSame('', $page->getPosition('right'));
    }
}
