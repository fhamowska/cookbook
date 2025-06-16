<?php

/*
 * This file is part of the User service test suite.
 */

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UserServiceTest.
 */
class UserServiceTest extends TestCase
{
    private UserRepository&MockObject $userRepository;
    private PaginatorInterface&MockObject $paginator;
    private UserService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->service = new UserService($this->userRepository, $this->paginator);
    }

    /**
     * Test getPaginatedList().
     */
    public function testGetPaginatedList(): void
    {
        $page = 1;
        $queryBuilder = $this->createMock(\Doctrine\ORM\QueryBuilder::class);
        $pagination = $this->createMock(PaginationInterface::class);

        $this->userRepository
            ->expects(self::once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, UserRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page);

        self::assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $user = new User();

        $this->userRepository
            ->expects(self::once())
            ->method('save')
            ->with($user);

        $this->service->save($user);
    }

    /**
     * Test findOneById().
     */
    public function testFindOneById(): void
    {
        $id = 42;
        $user = new User();

        $this->userRepository
            ->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn($user);

        $result = $this->service->findOneById($id);

        self::assertSame($user, $result);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $user = new User();

        $this->userRepository
            ->expects(self::once())
            ->method('delete')
            ->with($user);

        $this->service->delete($user);
    }
}
