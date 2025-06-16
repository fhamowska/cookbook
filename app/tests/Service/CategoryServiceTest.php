<?php

/*
 * Category service test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Service\CategoryService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class CategoryServiceTest.
 */
class CategoryServiceTest extends TestCase
{
    private CategoryRepository $categoryRepository;
    private PaginatorInterface $paginator;
    private RecipeRepository $recipeRepository;
    private CategoryService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);

        $this->service = new CategoryService(
            $this->categoryRepository,
            $this->paginator,
            $this->recipeRepository
        );
    }

    /**
     * Test getPaginatedList().
     */
    public function testGetPaginatedList(): void
    {
        $page = 1;
        $pagination = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->categoryRepository
            ->expects(self::once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, CategoryRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page);

        $this->assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $category = new Category();

        $this->categoryRepository
            ->expects(self::once())
            ->method('save')
            ->with($category);

        $this->service->save($category);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $category = new Category();

        $this->categoryRepository
            ->expects(self::once())
            ->method('delete')
            ->with($category);

        $this->service->delete($category);
    }

    /**
     * Test canBeDeleted() returns true.
     */
    public function testCanBeDeletedTrue(): void
    {
        $category = new Category();

        $this->recipeRepository
            ->expects(self::once())
            ->method('countByCategory')
            ->with($category)
            ->willReturn(0);

        $this->assertTrue($this->service->canBeDeleted($category));
    }

    /**
     * Test canBeDeleted() returns false.
     */
    public function testCanBeDeletedFalse(): void
    {
        $category = new Category();

        $this->recipeRepository
            ->expects(self::once())
            ->method('countByCategory')
            ->with($category)
            ->willReturn(5);

        $this->assertFalse($this->service->canBeDeleted($category));
    }

    /**
     * Test canBeDeleted() handles NoResultException.
     */
    public function testCanBeDeletedThrowsNoResultException(): void
    {
        $category = new Category();

        $this->recipeRepository
            ->expects(self::once())
            ->method('countByCategory')
            ->with($category)
            ->willThrowException(new NoResultException());

        $result = $this->service->canBeDeleted($category);
        $this->assertFalse($result);
    }

    /**
     * Test canBeDeleted() handles NonUniqueResultException.
     */
    public function testCanBeDeletedThrowsNonUniqueResultException(): void
    {
        $category = new Category();

        $this->recipeRepository
            ->expects(self::once())
            ->method('countByCategory')
            ->with($category)
            ->willThrowException(new NonUniqueResultException());

        $result = $this->service->canBeDeleted($category);
        $this->assertFalse($result);
    }

    /**
     * Test findOneById().
     */
    public function testFindOneById(): void
    {
        $category = new Category();

        $this->categoryRepository
            ->expects(self::once())
            ->method('find')
            ->with(42)
            ->willReturn($category);

        $result = $this->service->findOneById(42);
        $this->assertSame($category, $result);
    }
}
