<?php

/*
 * User repository test.
 */

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * Class UserRepositoryTest.
 */
class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(User::class);
    }

    /**
     * Test saving a User entity and retrieving it.
     */
    public function testSaveAndFind(): void
    {
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword('initialpassword');
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);

        $this->assertNotNull($user->getId());

        $foundUser = $this->userRepository->find($user->getId());

        $this->assertInstanceOf(User::class, $foundUser);
        $this->assertSame('testuser@example.com', $foundUser->getEmail());
    }

    /**
     * Test deleting a User entity.
     */
    public function testDelete(): void
    {
        $user = new User();
        $user->setEmail('deleteuser@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);

        $id = $user->getId();

        $this->userRepository->delete($user);

        $deletedUser = $this->userRepository->find($id);

        $this->assertNull($deletedUser);
    }

    /**
     * Test queryAll returns a QueryBuilder instance.
     */
    public function testQueryAll(): void
    {
        $qb = $this->userRepository->queryAll();

        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $qb);

        $results = $qb->getQuery()->getResult();

        $this->assertIsArray($results);
    }

    /**
     * Test upgrading a User's password.
     */
    public function testUpgradePassword(): void
    {
        $user = new User();
        $user->setEmail('passworduser@example.com');
        $user->setPassword('oldpassword');
        $user->setRoles(['ROLE_USER']);

        $this->userRepository->save($user);

        $newHashedPassword = 'newhashedpassword123';

        $this->userRepository->upgradePassword($user, $newHashedPassword);

        $updatedUser = $this->userRepository->find($user->getId());

        $this->assertSame($newHashedPassword, $updatedUser->getPassword());
    }

    /**
     * Test upgrading password with unsupported user throws exception.
     */
    public function testUpgradePasswordWithUnsupportedUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $mockUser = $this->createMock(PasswordAuthenticatedUserInterface::class);

        $this->userRepository->upgradePassword($mockUser, 'irrelevant');
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
    }
}
