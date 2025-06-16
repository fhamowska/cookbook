<?php

/*
 * Rating service test.
 */

namespace App\Tests\Service;

use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RatingRepository;
use App\Service\RatingService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RatingServiceTest.
 */
class RatingServiceTest extends TestCase
{
    private RatingRepository $ratingRepository;
    private PaginatorInterface $paginator;
    private RatingService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->ratingRepository = $this->createMock(RatingRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->service = new RatingService(
            $this->ratingRepository,
            $this->paginator
        );
    }

    /**
     * Test getPaginatedList() for admin user.
     */
    public function testGetPaginatedListAsAdmin(): void
    {
        $page = 1;
        $user = $this->createMock(User::class);
        $pagination = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $user->method('hasRole')
            ->with('ROLE_ADMIN')
            ->willReturn(true);

        $this->ratingRepository
            ->expects(self::once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, RatingRepository::PAGINATOR_ITEMS_PER_PAGE)
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
        $pagination = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $user->method('hasRole')
            ->with('ROLE_ADMIN')
            ->willReturn(false);

        $this->ratingRepository
            ->expects(self::once())
            ->method('queryByAuthor')
            ->with($user)
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, RatingRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page, $user);
        $this->assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $rating = new Rating();

        $this->ratingRepository
            ->expects(self::once())
            ->method('save')
            ->with($rating);

        $this->service->save($rating);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $rating = new Rating();

        $this->ratingRepository
            ->expects(self::once())
            ->method('delete')
            ->with($rating);

        $this->service->delete($rating);
    }

    /**
     * Test calculateAvg().
     */
    public function testCalculateAvg(): void
    {
        $recipe = new Recipe();
        $expectedAvg = 4.5;

        $this->ratingRepository
            ->expects(self::once())
            ->method('calculateAvg')
            ->with($recipe)
            ->willReturn($expectedAvg);

        $avg = $this->service->calculateAvg($recipe);
        $this->assertSame($expectedAvg, $avg);
    }

    /**
     * Test calculateAvg() throws exception.
     */
    public function testCalculateAvgThrowsException(): void
    {
        $this->expectException(NonUniqueResultException::class);

        $recipe = new Recipe();

        $this->ratingRepository
            ->expects(self::once())
            ->method('calculateAvg')
            ->with($recipe)
            ->willThrowException(new NonUniqueResultException());

        $this->service->calculateAvg($recipe);
    }
}
