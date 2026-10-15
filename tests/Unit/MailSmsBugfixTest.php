<?php

namespace Tests\Unit;

use Codeception\Test\Unit;
use Simflex\Core\ConsoleBase;
use Simflex\Core\Container;
use Simflex\Core\Core;
use Simflex\Core\Image;
use Simflex\Core\Mail;
use Simflex\Core\Sms;
use Simflex\Core\Sms\DriverFake;

class MailSmsBugfixTest extends Unit
{
    private array $siteParamsBackup = [];
    private mixed $configBackup = null;
    private array $serverBackup = [];
    private string $tmpRoot = '';

    protected function _before(): void
    {
        $prop = new \ReflectionProperty(Core::class, 'site_params');
        $this->siteParamsBackup = $prop->getValue();
        // Чтобы Core::siteParam() не ходил в БД
        $prop->setValue(null, ['site_name' => 'Test site', 'send_deny_email' => '', 'sender' => '']);

        $this->configBackup = Container::get('config');
        $this->serverBackup = $_SERVER;
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'example.test';

        $this->tmpRoot = sys_get_temp_dir() . '/mailsms_bugfix_' . uniqid();
        mkdir($this->tmpRoot, 0777, true);
    }

    protected function _after(): void
    {
        (new \ReflectionProperty(Core::class, 'site_params'))->setValue(null, $this->siteParamsBackup);
        Container::set('config', $this->configBackup);
        $_SERVER = $this->serverBackup;
        exec('rm -rf ' . escapeshellarg($this->tmpRoot));
    }

    private function mailData(Mail $mail): array
    {
        return (new \ReflectionProperty(Mail::class, 'data'))->getValue($mail);
    }

    public function testSetFromParsesNameAndEmail(): void
    {
        $mail = Mail::create('to@example.test', 'Subj', 'Body')->setFrom('Иванов Иван <ivanov@mail.ru>');
        $data = $this->mailData($mail);
        $this->assertSame('Иванов Иван', $data['from_name']);
        $this->assertSame('ivanov@mail.ru', $data['from_email']);
    }

    public function testSetReplyToParsesNameAndEmail(): void
    {
        $mail = Mail::create('to@example.test', 'Subj', 'Body')->setReplyTo('Петр <petr@mail.ru>');
        $data = $this->mailData($mail);
        $this->assertSame('Петр', $data['reply_name']);
        $this->assertSame('petr@mail.ru', $data['reply_email']);
    }

    public function testSendSmtpWithoutCredentialsReturnsFalseInsteadOfFatal(): void
    {
        Container::set('config', $this->makeConfig(['host' => '127.0.0.1', 'port' => '1']));
        $mail = Mail::create('to@example.test', 'Subj', 'Body');
        $this->assertFalse((new \ReflectionMethod(Mail::class, 'sendSMTP'))->invoke($mail));
    }

    public function testSendSmtpUsesPhpMailerAndReportsConnectionError(): void
    {
        Container::set('config', $this->makeConfig(['host' => '127.0.0.1', 'port' => '1']));
        $mail = Mail::create('to@example.test', 'Тема', 'Body')
            ->setSMTPSender('user@example.test', 'secret');
        // Порт 1 закрыт: PHPMailer бросает исключение, sendSMTP должен вернуть false
        $this->assertFalse((new \ReflectionMethod(Mail::class, 'sendSMTP'))->invoke($mail));
    }

    public function testSetSmtpSenderWithoutPasswordDoesNotWarn(): void
    {
        $mail = Mail::create('to@example.test', 'Subj', 'Body')->setSMTPSender('user@example.test');
        $this->assertSame(['login' => 'user@example.test', 'pass' => ''], $this->mailData($mail)['smtp']);
    }

    public function testImageSizesAreDeclaredAndPreviewAdded(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = $this->tmpRoot;
        $this->assertTrue(property_exists(Image::class, 'sizes'));
        $this->assertTrue(property_exists(Image::class, 'img'));

        $img = new Image('test/', ['small' => '200x200']);
        $this->assertSame('90x90', $img->sizes['preview']);
        $this->assertSame('200x200', $img->sizes['small']);

        $img = new Image('test/', ['preview' => '120x120']);
        $this->assertSame('120x120', $img->sizes['preview']);
    }

    public function testImageSaveSvgDoesNotCallGd(): void
    {
        $_SERVER['DOCUMENT_ROOT'] = $this->tmpRoot;
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="1" height="1"/>';
        $src = $this->tmpRoot . '/upload.svg';
        file_put_contents($src, $svg);

        $img = new Image('svg/', ['small' => '200x200']);
        $img->type = 'svg';
        (new \ReflectionProperty(Image::class, 'img'))->setValue($img, $svg);
        (new \ReflectionProperty(Image::class, 'uploadedFilePath'))->setValue($img, $src);
        $img->save();

        foreach (['raw', 'small', 'preview', 'source'] as $subdir) {
            $file = $img->path . $subdir . '/' . $img->name . '.svg';
            $this->assertFileExists($file);
            $this->assertSame($svg, file_get_contents($file));
        }
    }

    public function testFindJobsCountReturnsInt(): void
    {
        $method = new \ReflectionMethod(ConsoleBase::class, 'findJobsCount');
        $this->assertSame(0, $method->invoke(null, 'no_such_job_' . uniqid()));
    }

    public function testSmsDriverResolvedFromCoreSmsNamespace(): void
    {
        $method = new \ReflectionMethod(Sms::class, 'createDriverByInfo');
        $driver = $method->invoke(null, ['driver_id' => 1, 'alias' => 'fake']);
        $this->assertInstanceOf(DriverFake::class, $driver);
        $this->assertNull($method->invoke(null, ['driver_id' => 2, 'alias' => 'nonexistent']));
        $this->assertNull($method->invoke(null, []));
    }

    public function testDriverFakeReturnsResultArray(): void
    {
        $result = (new DriverFake(['driver_id' => 1]))->send('79001002030', 'text', 'from', 5);
        $this->assertIsArray($result);
        $this->assertTrue($result['success']);
        $this->assertSame('', $result['error_text']);
        $this->assertArrayHasKey('driver_sms_id', $result);
        $this->assertArrayHasKey('price', $result);
    }

    public function testDriverSmppHasNoUndefinedReferences(): void
    {
        $source = file_get_contents((new \ReflectionClass(\Simflex\Core\Sms\DriverSmpp::class))->getFileName());
        $this->assertStringNotContainsString('PlugPhone', $source);
        $this->assertStringNotContainsString('$smpp', $source);
        $this->assertStringNotContainsString('CigMall', $source);
    }

    private function makeConfig(array $mail): object
    {
        // ConfigBase нельзя инстанцировать без констант приложения (SF_ROOT_PATH); sendSMTP читает только ->mail
        $defaults = ['transport' => null, 'host' => 'localhost', 'port' => '25', 'security' => '', 'username' => '', 'password' => ''];
        return (object)['mail' => array_merge($defaults, $mail)];
    }
}
