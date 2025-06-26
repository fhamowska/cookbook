<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testIdInitiallyNull(): void
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    public function testEmailCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $this->assertSame('user@example.com', $user->getEmail());
        $this->assertSame('user@example.com', $user->getUserIdentifier());
        $this->assertSame('user@example.com', $user->getUsername());
    }

    public function testPasswordCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $user->setPassword('secure_password');

        $this->assertSame('secure_password', $user->getPassword());
    }

    public function testRolesIncludeRoleUserByDefault(): void
    {
        $user = new User();
        $this->assertContains(UserRole::ROLE_USER->value, $user->getRoles());
    }

    public function testRolesCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $user->setRoles([UserRole::ROLE_ADMIN->value]);

        $roles = $user->getRoles();
        $this->assertContains(UserRole::ROLE_ADMIN->value, $roles);
        $this->assertContains(UserRole::ROLE_USER->value, $roles);
    }

    public function testHasRole(): void
    {
        $user = new User();
        $user->setRoles([UserRole::ROLE_USER->value]);

        $this->assertTrue($user->hasRole(UserRole::ROLE_USER->value));
        $this->assertFalse($user->hasRole(UserRole::ROLE_ADMIN->value));
    }

    public function testGetSaltReturnsNull(): void
    {
        $user = new User();
        $this->assertNull($user->getSalt());
    }

    public function testEraseCredentialsDoesNotThrow(): void
    {
        $user = new User();
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    public function testToStringReturnsEmail(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $this->assertSame('user@example.com', (string)$user);
    }
}
