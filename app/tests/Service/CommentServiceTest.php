<?php

/*
 * Comment service test.
 */

namespace App\Tests\Service;

use App\Entity\Comment;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Service\CommentService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentServiceTest.
 */
class CommentServiceTest extends TestCase
{
    private CommentRepository $commentRepository;
    private PaginatorInterface $paginator;
    private CommentService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->commentRepository = $this->createMock(CommentRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->service = new CommentService($this->commentRepository, $this->paginator);
    }

    /**
     * Test getPaginatedList() for admin user.
     */
    public function testGetPaginatedListAsAdmin(): void
    {
        $page = 1;
        $user = $this->createMock(User::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $pagination = $this->createMock(PaginationInterface::class);

        $user->method('hasRole')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $this->commentRepository
            ->expects(self::once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, CommentRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page, $user);

        $this->assertSame($pagination, $result);
    }

    /**
     * Test getPaginatedList() for non-admin user.
     */
    public function testGetPaginatedListAsNonAdmin(): void
    {
        $page = 1;
        $user = $this->createMock(User::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $pagination = $this->createMock(PaginationInterface::class);

        $user->method('hasRole')
            ->with('ROLE_ADMIN')
            ->willReturn(false);

        $this->commentRepository
            ->expects(self::once())
            ->method('queryByAuthor')
            ->with($user)
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, CommentRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page, $user);

        $this->assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $comment = new Comment();

        $this->commentRepository
            ->expects(self::once())
            ->method('save')
            ->with($comment);

        $this->service->save($comment);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $comment = new Comment();

        $this->commentRepository
            ->expects(self::once())
            ->method('delete')
            ->with($comment);

        $this->service->delete($comment);
    }
}
