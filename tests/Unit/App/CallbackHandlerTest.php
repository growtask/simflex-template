<?php

namespace Tests\Unit\App;

use App\Extensions\Form\CallbackHandler;
use App\Extensions\Form\Notifiers\Notifier;
use Codeception\Test\Unit;
use Simflex\Core\Extensions\Form\FormGuard;
use Simflex\Core\Http\Request;
use Simflex\Core\System\Container;

/**
 * Приём формы «Связаться с нами»: проверка на сервере, защита от ботов, отправка по каналам (подменённым)
 */
class CallbackHandlerTest extends Unit
{
    private mixed $request = null;

    protected function _before(): void
    {
        $this->request = Container::isSet('request') ? Container::get('request') : null;
    }

    protected function _after(): void
    {
        Container::set('request', $this->request);
    }

    /**
     * @param array<string,string> $post
     */
    private function request(array $post = [], bool $withToken = true): Request
    {
        $guard = new FormGuard();
        $post += [
            'form' => 'callback', 'name' => 'Иван', 'phone' => '+7 999 123-45-67', 'email' => 'a@example.com',
            'comment' => 'Привет', 'page' => '/', 'consent' => '1',
        ];
        if ($withToken) {
            // токен выдан давно, форма не отправлена мгновенно
            $post += [FormGuard::TOKEN_FIELD => $guard->issueToken(time() - 60)];
        }

        return Request::create('/form/callback', [], $post);
    }

    private function notifier(bool $configured = true, bool $result = true): Notifier
    {
        return new class ($configured, $result) implements Notifier {
            /** @var list<array<string,string>> */
            public array $sent = [];

            public function __construct(private bool $configured, private bool $result)
            {
            }

            public function isConfigured(): bool
            {
                return $this->configured;
            }

            public function send(array $data): bool
            {
                $this->sent[] = $data;
                return $this->result;
            }
        };
    }

    public function testSuccessfulSendGoesToConfiguredChannels(): void
    {
        $mail = $this->notifier();
        $telegram = $this->notifier(false);

        $answer = (new CallbackHandler([$mail, $telegram]))->handle($this->request());

        $this->assertTrue($answer['success']);
        $this->assertCount(1, $mail->sent);
        $this->assertSame('Иван', $mail->sent[0]['name']);
        $this->assertSame('+7 999 123-45-67', $mail->sent[0]['phone']);
        $this->assertSame([], $telegram->sent, 'Ненастроенный канал не вызывается');
    }

    public function testValidationErrors(): void
    {
        $channel = $this->notifier();
        $request = $this->request(['name' => ' ', 'phone' => '12', 'email' => 'bad', 'comment' => str_repeat('я', 301), 'consent' => '']);

        $answer = (new CallbackHandler([$channel]))->handle($request);

        $this->assertFalse($answer['success']);
        $this->assertSame(['name', 'phone', 'email', 'comment', 'consent'], array_keys($answer['errors']));
        $this->assertSame([], $channel->sent, 'С ошибками заявка не отправляется');
    }

    public function testConsentIsRequired(): void
    {
        $answer = (new CallbackHandler([$this->notifier()]))->handle($this->request(['consent' => '']));

        $this->assertFalse($answer['success']);
        $this->assertSame(['consent'], array_keys($answer['errors']));
    }

    public function testEmailIsOptional(): void
    {
        $answer = (new CallbackHandler([$this->notifier()]))->handle($this->request(['email' => '']));

        $this->assertTrue($answer['success']);
    }

    public function testTrapAndTokenReject(): void
    {
        $channel = $this->notifier();
        $handler = new CallbackHandler([$channel]);

        $trap = $handler->handle($this->request([FormGuard::TRAP_FIELD => 'http://spam']));
        $noToken = $handler->handle($this->request(withToken: false));
        $fast = $handler->handle($this->request([FormGuard::TOKEN_FIELD => (new FormGuard())->issueToken()]));
        $forged = $handler->handle($this->request([FormGuard::TOKEN_FIELD => time() - 60 . '.deadbeef']));

        foreach ([$trap, $noToken, $fast, $forged] as $answer) {
            $this->assertFalse($answer['success']);
            $this->assertSame([], $answer['errors']);
        }
        $this->assertSame([], $channel->sent);
    }

    public function testAllChannelsFailedIsError(): void
    {
        $answer = (new CallbackHandler([$this->notifier(true, false), $this->notifier(true, false)]))->handle($this->request());

        $this->assertFalse($answer['success']);
    }

    public function testOneFailedChannelIsStillSuccess(): void
    {
        $ok = $this->notifier();
        $answer = (new CallbackHandler([$this->notifier(true, false), $ok]))->handle($this->request());

        $this->assertTrue($answer['success']);
        $this->assertCount(1, $ok->sent);
    }

    public function testGetRequestIsRejected(): void
    {
        $answer = (new CallbackHandler([$this->notifier()]))->handle(Request::create('/form/callback'));

        $this->assertFalse($answer['success']);
    }
}
