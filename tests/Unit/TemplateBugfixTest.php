<?php

namespace Tests\Unit;

use App\Core\Simflex;
use App\Extensions\Form\Component\AjaxForm;
use App\Layout\LayoutBase;
use App\Layout\LayoutManager;
use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\EventManager;
use Simflex\Core\Mail\Mail;
use Simflex\Core\Mail\Mailer;
use Simflex\Core\Mail\Transport;

class TemplateBugfixTest extends Unit
{
    private array $siteParams = [];

    protected function _before(): void
    {
        $this->siteParams = $this->getSiteParams();
    }

    protected function _after(): void
    {
        $this->setSiteParams($this->siteParams);
    }

    private function getSiteParams(): array
    {
        $prop = new \ReflectionProperty(Core::class, 'siteParams');
        return $prop->getValue();
    }

    private function setSiteParams(array $params): void
    {
        $prop = new \ReflectionProperty(Core::class, 'siteParams');
        $prop->setValue(null, $params);
    }

    public function testLayoutDrawWithoutNoGenFlagDoesNotWarn(): void
    {
        $dir = sys_get_temp_dir() . '/sf-layout-' . uniqid();
        mkdir($dir);
        file_put_contents($dir . '/layout.tpl', 'layout:<?= $data["x"] ?>');
        TemplateBugfixTestLayout::$dir = $dir;

        $prev = LayoutManager::$shouldGenerateFields;
        LayoutManager::$shouldGenerateFields = true;
        set_error_handler(function (int $no, string $str) {
            throw new \ErrorException($str, 0, $no);
        });
        try {
            $this->assertSame('layout:ok', TemplateBugfixTestLayout::draw(['x' => 'ok']));
        } finally {
            restore_error_handler();
            LayoutManager::$shouldGenerateFields = $prev;
            unlink($dir . '/layout.tpl');
            rmdir($dir);
        }
    }

    public function testPrepareOutputReplacesSiteParams(): void
    {
        $this->setSiteParams(['phone' => '123', 'empty' => null]);
        if (!Container::isSet('events')) {
            Container::set('events', new EventManager());
        }

        $sf = (new \ReflectionClass(Simflex::class))->newInstanceWithoutConstructor();
        $cfg = new \ReflectionProperty(Simflex::class, 'cfg');
        $root = dirname(__DIR__, 2);
        defined('SF_CORE_ROOT_PATH') || require_once $root . '/vendor/growtask/simflex/src/constants.php';
        defined('SF_ROOT_PATH') || require_once $root . '/constants.php';
        require_once $root . '/config.php';
        $config = (new \ReflectionClass(\App\Config::class))->newInstanceWithoutConstructor();
        $config->devMode = true;
        $cfg->setValue($sf, $config);

        $this->assertSame(
            'call 123 () ' . date('Y'),
            $sf->prepareOutput('call {phone} ({empty}) {year}')
        );
    }

    public function testAjaxFormSendsMailViaMailer(): void
    {
        $this->setSiteParams(['form_email' => 'to@example.com', 'form_tg_token' => '']);
        $transport = new TemplateBugfixTestTransport();
        $mailer = new Mailer();
        $mailer->transport = $transport;
        $hadMailer = Container::isSet('mailer');
        $oldMailer = $hadMailer ? Container::get('mailer') : null;
        Container::set('mailer', $mailer);

        try {
            $form = (new \ReflectionClass(TemplateBugfixTestForm::class))->newInstanceWithoutConstructor();
            $form->setData([
                'type' => 'Test', 'from' => '/page', 'fromTitle' => 'Page', 'name' => '<b>Bob</b>',
                'email' => 'bob@example.com', 'phone' => '1', 'message' => 'hi',
            ]);

            $this->assertTrue($form->callSendMail());
            $this->assertTrue($form->callSendTelegram(), 'Telegram is skipped when token is not configured');
            $this->assertCount(1, $transport->sent);
            $this->assertSame('to@example.com', $transport->sent[0]->to);
            $this->assertSame('Новая заявка с сайта', $transport->sent[0]->subject);
            $this->assertStringContainsString('&lt;b&gt;Bob&lt;/b&gt;', $transport->sent[0]->body);

            $transport->throw = true;
            $this->assertFalse($form->callSendMail());
            $this->assertSame(['mail: boom'], $form->getErrors());
        } finally {
            Container::set('mailer', $oldMailer);
        }
    }
}

class TemplateBugfixTestLayout extends LayoutBase
{
    public static string $dir = '';

    protected static function layoutRootDir(): string
    {
        return static::$dir;
    }
}

class TemplateBugfixTestTransport implements Transport
{
    public array $sent = [];
    public bool $throw = false;

    public function send(Mail $mail): bool
    {
        if ($this->throw) {
            throw new \RuntimeException('boom');
        }
        $this->sent[] = $mail;
        return true;
    }
}

class TemplateBugfixTestForm extends AjaxForm
{
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function callSendMail(): bool
    {
        return $this->sendMail();
    }

    public function callSendTelegram(): bool
    {
        return $this->sendTelegram();
    }
}
