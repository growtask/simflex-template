<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Models\User;

final class ModelAccessTest extends Unit
{
    public function testCamelCaseColumnsWorkBeforeInsertAndKeepFalsyValues(): void
    {
        $newUser = new User()->fill(['role_id' => 3, 'is_off' => false, 'phone_number' => '', 'time_create' => null]);
        $this->assertSame(3, $newUser->roleId);
        $this->assertFalse($newUser->isOff);
        $this->assertSame('', $newUser->phoneNumber);
        $this->assertNull($newUser->timeCreate);

        $loadedUser = new User()->fill(['user_id' => 7, 'role_id' => 0, 'is_off' => false, 'phone_number' => '']);
        $this->assertSame(0, $loadedUser->roleId);
        $this->assertFalse($loadedUser->isOff);
        $this->assertSame('', $loadedUser['phoneNumber']);
    }
}
