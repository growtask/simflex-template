<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Http\Html\HtmlRequest;
use Simflex\Core\Http\Html\HtmlResponse;
use Simflex\Core\Http\Page;
use Simflex\Core\System\Container;
use Simflex\Core\System\Paths;

final class PageResponseTest extends Unit
{
    public function testHeadersPrecedeThemePrintAndAjaxBody(): void
    {
        $registry = new \ReflectionProperty(Container::class, 'registry');
        $previous = $registry->getValue();
        $root = sys_get_temp_dir() . '/page_response_' . bin2hex(random_bytes(6));
        mkdir($root . '/theme/default', 0770, true);
        $template = '<?php if (!\\Simflex\\Core\\System\\Container::getResponse()->isSent) '
            . '{ throw new \\RuntimeException("Headers missing"); } ?>body';
        try {
            file_put_contents($root . '/theme/default/index.tpl', $template);
            file_put_contents($root . '/theme/default/print.tpl', $template);
            Container::set('paths', new Paths($root));
            Container::set('config', (object)['theme' => 'default']);
            $this->expectOutputString('bodybodybody');
            foreach (['theme', 'print', 'ajax'] as $mode) {
                $response = new class () extends HtmlResponse {
                    public bool $isSent = false;

                    public function outputHeaders(): void
                    {
                        $this->isSent = true;
                    }
                };
                $response->setStatusCode(422)->setHeader('Cache-Control', 'private, no-store');
                Container::set('response', $response);
                Container::set('request', HtmlRequest::create($mode === 'print' ? '/?print=1' : '/'));
                $page = new class ($mode === 'ajax') extends Page {
                    public function __construct(bool $isAjax)
                    {
                        $this->contentOnly = $isAjax;
                    }

                    public function getContent(): string
                    {
                        if (!Container::getResponse()->isSent) {
                            throw new \RuntimeException('Headers missing');
                        }
                        return 'body';
                    }
                };
                $page->execute();
                $this->assertTrue($response->isSent);
                $this->assertSame(422, $response->getStatusCode());
            }
        } finally {
            foreach (['index.tpl', 'print.tpl'] as $file) {
                if (is_file($root . '/theme/default/' . $file)) {
                    unlink($root . '/theme/default/' . $file);
                }
            }
            rmdir($root . '/theme/default');
            rmdir($root . '/theme');
            rmdir($root);
            $registry->setValue(null, $previous);
        }
    }
}
