<?php

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Sms\Sms;
use Simflex\Core\Sms\DriverFake;

class SmsTest extends Unit
{
    public function testDriverIsResolvedByAlias(): void
    {
        $method = new \ReflectionMethod(Sms::class, 'createDriverByInfo');
        $this->assertInstanceOf(DriverFake::class, $method->invoke(null, ['driver_id' => 1, 'alias' => 'fake']));
        $this->assertNull($method->invoke(null, ['driver_id' => 2, 'alias' => 'nonexistent']));
        $this->assertNull($method->invoke(null, []));
    }

    public function testFakeDriverReturnsResult(): void
    {
        $result = (new DriverFake(['driver_id' => 1]))->send('79001002030', 'text', 'from', 5);
        $this->assertTrue($result['success']);
        $this->assertSame('', $result['error_text']);
        $this->assertArrayHasKey('driver_sms_id', $result);
        $this->assertArrayHasKey('price', $result);
    }
}
