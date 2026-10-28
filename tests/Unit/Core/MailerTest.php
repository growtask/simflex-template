<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use Simflex\Core\Container;
use Simflex\Mail\Mail;
use Simflex\Mail\Mailer;
use Simflex\Mail\Transport;
use Simflex\Mail\Transports\PhpMailer;
use Simflex\Core\SiteSettings;

/**
 * Почта: Mailer и письмо, транспорт по умолчанию - SMTP через PHPMailer
 */
class MailerTest extends Unit
{
    private ?array $siteParams = null;
    private mixed $config = null;

    protected function _before(): void
    {
        $prop = new \ReflectionProperty(SiteSettings::class, 'values');
        $this->siteParams = $prop->getValue();
        // тогда SiteSettings::getAll() не обращается к БД
        $prop->setValue(null, ['site_name' => 'Test site']);
        $this->config = Container::isSet('config') ? Container::get('config') : null;
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(SiteSettings::class, 'values'))->setValue(null, $this->siteParams);
        Container::set('config', $this->config);
    }

    public function testPlainTextIsEscapedInHtmlBody(): void
    {
        $transport = new MailerTestTransport();
        $this->assertTrue((new Mailer($transport))->sendPlain('Тема', 'to@example.test', "Имя: <b>Bob</b>\nТелефон: 1"));

        $mail = $transport->sent[0];
        $this->assertSame('to@example.test', $mail->to);
        $this->assertSame('Тема', $mail->subject);
        $this->assertSame("Имя: &lt;b&gt;Bob&lt;/b&gt;<br />\nТелефон: 1", $mail->body);
        $this->assertSame("Имя: <b>Bob</b>\nТелефон: 1", $mail->altBody);
    }

    public function testTemplateBodyAndTextAlternative(): void
    {
        $tpl = tempnam(sys_get_temp_dir(), 'mail') . '.tpl';
        file_put_contents($tpl, '<p>Здравствуйте, <b><?= $data[\'name\'] ?></b></p><p>Строка<br>вторая</p>');
        try {
            $transport = new MailerTestTransport();
            (new Mailer($transport))->sendTemplate('Тема', 'to@example.test', $tpl, ['name' => 'Bob']);
        } finally {
            unlink($tpl);
        }

        $this->assertSame('<p>Здравствуйте, <b>Bob</b></p><p>Строка<br>вторая</p>', $transport->sent[0]->body);
        $this->assertSame("Здравствуйте, Bob\nСтрока\nвторая", $transport->sent[0]->altBody);
    }

    public function testServiceFromContainerOrNewWithConfigTransport(): void
    {
        $this->setMailConfig(['transport' => MailerTestTransport::class]);
        $this->assertInstanceOf(MailerTestTransport::class, Mailer::get()->transport);

        $this->setMailConfig([]);
        $this->assertInstanceOf(PhpMailer::class, (new Mailer())->transport);
    }

    public function testSmtpConnectionErrorThrows(): void
    {
        // порт 1 закрыт
        $this->setMailConfig(['host' => '127.0.0.1', 'port' => '1', 'username' => 'user@example.test', 'password' => 'secret']);

        $this->expectException(PHPMailerException::class);
        (new Mailer(new PhpMailer()))->sendText('Тема', 'to@example.test', 'Body');
    }

    private function setMailConfig(array $mail): void
    {
        $defaults = ['transport' => null, 'host' => 'localhost', 'port' => '25', 'security' => '', 'username' => '', 'password' => ''];
        Container::set('config', (object)['mail' => array_merge($defaults, $mail), 'devMode' => false, 'enableLogging' => false]);
    }
}

class MailerTestTransport implements Transport
{
    /** @var Mail[] */
    public array $sent = [];

    public function send(Mail $mail): bool
    {
        $this->sent[] = $mail;
        return true;
    }
}
