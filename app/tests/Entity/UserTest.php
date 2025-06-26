<?php

/*
 * User entity test.
 */

namespace App\Tests\Entity;

use App\Entity\User;
use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

/**
 * UserTest class.
 */
class UserTest extends TestCase
{
    /**
     * Test that ID is initially null.
     */
    public function testIdInitiallyNull(): void
    {
        $user = new User();
        $this->assertNull($user->getId());
    }

    /**
     * Test setting and getting email and username identifiers.
     */
    public function testEmailCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $this->assertSame('user@example.com', $user->getEmail());
        $this->assertSame('user@example.com', $user->getUserIdentifier());
        $this->assertSame('user@example.com', $user->getUsername());
    }

    /**
     * Test setting and getting password.
     */
    public function testPasswordCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $user->setPassword('secure_password');

        $this->assertSame('secure_password', $user->getPassword());
    }

    /**
     * Test default roles include ROLE_USER.
     */
    public function testRolesIncludeRoleUserByDefault(): void
    {
        $user = new User();
        $this->assertContains(UserRole::ROLE_USER->value, $user->getRoles());
    }

    /**
     * Test setting and retrieving roles.
     */
    public function testRolesCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $user->setRoles([UserRole::ROLE_ADMIN->value]);

        $roles = $user->getRoles();
        $this->assertContains(UserRole::ROLE_ADMIN->value, $roles);
        $this->assertContains(UserRole::ROLE_USER->value, $roles);
    }

    /**
     * Test hasRole method.
     */
    public function testHasRole(): void
    {
        $user = new User();
        $user->setRoles([UserRole::ROLE_USER->value]);

        $this->assertTrue($user->hasRole(UserRole::ROLE_USER->value));
        $this->assertFalse($user->hasRole(UserRole::ROLE_ADMIN->value));
    }

    /**
     * Test getSalt returns null.
     */
    public function testGetSaltReturnsNull(): void
    {
        $user = new User();
        $this->assertNull($user->getSalt());
    }

    /**
     * Test eraseCredentials does not throw exception.
     */
    public function testEraseCredentialsDoesNotThrow(): void
    {
        $user = new User();
        $user->eraseCredentials();
        $this->assertTrue(true);
    }

    /**
     * Test __toString returns the email.
     */
    public function testToStringReturnsEmail(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');

        $this->assertSame('user@example.com', (string) $user);
    }
}
