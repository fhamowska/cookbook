<?php

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

/**
 * Class UserRoleTest.
 */
class UserRoleTest extends TestCase
{
    public function testRoleUserLabel(): void
    {
        $this->assertEquals('label.role_user', UserRole::ROLE_USER->label());
    }

    public function testRoleAdminLabel(): void
    {
        $this->assertEquals('label.role_admin', UserRole::ROLE_ADMIN->label());
    }

    public function testRoleValues(): void
    {
        $this->assertSame('ROLE_USER', UserRole::ROLE_USER->value);
        $this->assertSame('ROLE_ADMIN', UserRole::ROLE_ADMIN->value);
    }
}
