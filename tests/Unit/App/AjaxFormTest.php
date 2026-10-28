<?php

namespace Tests\Unit\App;

use App\Extensions\Form\Components\AjaxForm;
use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Mail\Mail;
use Simflex\Mail\Mailer;
use Simflex\Mail\Transport;
use Simflex\Core\SiteSettings;

/**
 * Форма заявки сайта: заявка отправляется на e-mail (и в Telegram, если он настроен)
 */
class AjaxFormTest extends Unit
{
    private ?array $siteParams = null;
    private mixed $mailer = null;

    protected function _before(): void
    {
        $this->siteParams = (new \ReflectionProperty(SiteSettings::class, 'values'))->getValue();
        $this->mailer = Container::isSet('mailer') ? Container::get('mailer') : null;
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(SiteSettings::class, 'values'))->setValue(null, $this->siteParams);
        Container::set('mailer', $this->mailer);
    }

    public function testRequestIsSentViaMailer(): void
    {
        (new \ReflectionProperty(SiteSettings::class, 'values'))->setValue(null, ['form_email' => 'to@example.com', 'form_tg_token' => '']);
        $transport = new AjaxFormTestTransport();
        $mailer = new Mailer();
        $mailer->transport = $transport;
        Container::set('mailer', $mailer);

        $form = (new \ReflectionClass(AjaxFormTestForm::class))->newInstanceWithoutConstructor();
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
    }
}

class AjaxFormTestTransport implements Transport
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

class AjaxFormTestForm extends AjaxForm
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
