<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\Mail;

class MailTest extends Unit
{
    private array $siteParamsBackup = [];
    private mixed $configBackup = null;
    private array $serverBackup = [];

    protected function _before(): void
    {
        $prop = new \ReflectionProperty(Core::class, 'siteParams');
        $this->siteParamsBackup = $prop->getValue();
        // тогда Core::siteParam() не обращается к БД
        $prop->setValue(null, ['site_name' => 'Test site', 'send_deny_email' => '', 'sender' => '']);
        $this->configBackup = Container::get('config');
        $this->serverBackup = $_SERVER;
        $_SERVER['HTTP_HOST'] ??= 'example.test';
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(Core::class, 'siteParams'))->setValue(null, $this->siteParamsBackup);
        Container::set('config', $this->configBackup);
        $_SERVER = $this->serverBackup;
    }

    public function testSetFromParsesNameAndEmail(): void
    {
        $data = $this->mailData(Mail::create('to@example.test', 'Subj', 'Body')->setFrom('Иванов Иван <ivanov@mail.ru>'));
        $this->assertSame('Иванов Иван', $data['from_name']);
        $this->assertSame('ivanov@mail.ru', $data['from_email']);
    }

    public function testSetReplyToParsesNameAndEmail(): void
    {
        $data = $this->mailData(Mail::create('to@example.test', 'Subj', 'Body')->setReplyTo('Петр <petr@mail.ru>'));
        $this->assertSame('Петр', $data['reply_name']);
        $this->assertSame('petr@mail.ru', $data['reply_email']);
    }

    public function testSetSmtpSenderWithoutPassword(): void
    {
        $mail = Mail::create('to@example.test', 'Subj', 'Body')->setSMTPSender('user@example.test');
        $this->assertSame(['login' => 'user@example.test', 'pass' => ''], $this->mailData($mail)['smtp']);
    }

    public function testSmtpWithoutCredentialsIsNotSent(): void
    {
        $this->setMailConfig(['host' => '127.0.0.1', 'port' => '1']);
        $this->assertFalse($this->sendSmtp(Mail::create('to@example.test', 'Subj', 'Body')));
    }

    public function testSmtpConnectionErrorIsReported(): void
    {
        // порт 1 закрыт: PHPMailer бросает исключение, sendSMTP() возвращает false
        $this->setMailConfig(['host' => '127.0.0.1', 'port' => '1']);
        $mail = Mail::create('to@example.test', 'Тема', 'Body')->setSMTPSender('user@example.test', 'secret');
        $this->assertFalse($this->sendSmtp($mail));
    }

    private function mailData(Mail $mail): array
    {
        return (new \ReflectionProperty(Mail::class, 'data'))->getValue($mail);
    }

    private function sendSmtp(Mail $mail): bool
    {
        return (new \ReflectionMethod(Mail::class, 'sendSMTP'))->invoke($mail);
    }

    private function setMailConfig(array $mail): void
    {
        // sendSMTP() читает только ->mail
        $defaults = ['transport' => null, 'host' => 'localhost', 'port' => '25', 'security' => '', 'username' => '', 'password' => ''];
        Container::set('config', (object)['mail' => array_merge($defaults, $mail)]);
    }
}
