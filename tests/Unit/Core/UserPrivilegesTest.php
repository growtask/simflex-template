<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use Codeception\Test\Unit;
use Simflex\Core\Models\User;
use Simflex\Core\System\Container;
use Simflex\Core\System\Privilege;
use Tests\Support\MockDb;

final class UserPrivilegesTest extends Unit
{
    use MockDb;

    public function testPrivilegeColumnsAreQualifiedInBothSubqueries(): void
    {
        $this->mockRows([]);
        Container::getConfig()->privilegeClass = Privilege::class;
        Privilege::reset();
        try {
            $query = User::findAdv()->modify('hasPrivileges', [Privilege::ADMIN]);
            $sql = (string)$query;
            $this->assertStringContainsString('user_privilege_personal.privilege IN(', $sql);
            $this->assertStringContainsString('user_role_privilege.privilege IN(', $sql);
        } finally {
            Privilege::reset();
        }
    }
}
