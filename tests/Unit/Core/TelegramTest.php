<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Curl\Curl;
use Simflex\Core\System\ConfigBase;
use Simflex\Core\System\Container;
use Simflex\Core\Telegram\TelegramApi;

class TelegramTest extends Unit
{
    private mixed $previousConfig;

    protected function _before(): void
    {
        $this->previousConfig = Container::isSet('config') ? Container::get('config') : null;
    }

    protected function _after(): void
    {
        Container::set('config', $this->previousConfig);
    }

    public function testProxyIsAppliedToMessageRequest(): void
    {
        $this->checkRequest('socks5h://proxy.example:1080');
    }

    public function testEmptyProxyKeepsDirectConnection(): void
    {
        $this->checkRequest('');
    }

    public function testMissingProxyKeepsDirectConnection(): void
    {
        $this->checkRequest(null);
    }

    private function checkRequest(?string $proxy): void
    {
        $config = new class () extends ConfigBase {
            public function load(): void
            {
            }
        };
        $config->telegram = ['token' => 'test-token'];
        if ($proxy !== null) {
            $config->telegram['proxy'] = $proxy;
        }
        Container::set('config', $config);
        $client = $this->getMockBuilder(Curl::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setOpt', 'post', 'getResponse'])
            ->getMock();
        if ($proxy) {
            $client->expects($this->once())->method('setOpt')->with(CURLOPT_PROXY, $proxy);
        } else {
            $client->expects($this->never())->method('setOpt');
        }
        $client->expects($this->once())->method('post')->with(
            'https://api.telegram.org/bottest-token/sendMessage',
            ['chat_id' => '123', 'parse_mode' => 'MarkdownV2', 'text' => 'test'],
        );
        $client->method('getResponse')->willReturn('{"ok":true}');
        $telegram = new class ($client) extends TelegramApi {
            public function __construct(private Curl $client)
            {
            }

            protected function createClient(): Curl
            {
                return $this->client;
            }
        };
        $this->assertTrue($telegram->send('123', 'test'));
    }
}
