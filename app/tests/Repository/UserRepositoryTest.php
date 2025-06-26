<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->em->getRepository(User::class);
    }

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

    public function testQueryAll(): void
    {
        $qb = $this->userRepository->queryAll();

        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $qb);

        $results = $qb->getQuery()->getResult();

        $this->assertIsArray($results);
    }

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

    public function testUpgradePasswordWithUnsupportedUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $mockUser = $this->createMock(PasswordAuthenticatedUserInterface::class);

        $this->userRepository->upgradePassword($mockUser, 'irrelevant');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->em->close();
    }
}
