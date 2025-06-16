<?php

/*
 * Recipe service test.
 */

namespace App\Tests\Service;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Repository\RecipeRepository;
use App\Service\RecipeService;
use App\Service\CategoryServiceInterface;
use App\Service\TagServiceInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class RecipeServiceTest.
 */
class RecipeServiceTest extends TestCase
{
    private CategoryServiceInterface $categoryService;
    private PaginatorInterface $paginator;
    private TagServiceInterface $tagService;
    private RecipeRepository $recipeRepository;
    private RecipeService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->categoryService = $this->createMock(CategoryServiceInterface::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->tagService = $this->createMock(TagServiceInterface::class);
        $this->recipeRepository = $this->createMock(RecipeRepository::class);

        $this->service = new RecipeService(
            $this->categoryService,
            $this->paginator,
            $this->tagService,
            $this->recipeRepository
        );
    }

    /**
     * Test getPaginatedList() without filters.
     *
     * @throws NonUniqueResultException
     */
    public function testGetPaginatedListNoFilters(): void
    {
        $page = 1;
        $filters = [];
        $pagination = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->recipeRepository
            ->expects(self::once())
            ->method('queryAll')
            ->with([])
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, RecipeRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page, $filters);
        self::assertSame($pagination, $result);
    }

    /**
     * Test getPaginatedList() with filters.
     *
     * @throws NonUniqueResultException
     */
    public function testGetPaginatedListWithFilters(): void
    {
        $page = 2;
        $filters = ['category_id' => 5, 'tag_id' => 9];
        $pagination = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $category = new Category();
        $tag = new Tag();

        $this->categoryService
            ->expects(self::once())
            ->method('findOneById')
            ->with($filters['category_id'])
            ->willReturn($category);

        $this->tagService
            ->expects(self::once())
            ->method('findOneById')
            ->with($filters['tag_id'])
            ->willReturn($tag);

        $this->recipeRepository
            ->expects(self::once())
            ->method('queryAll')
            ->with(['category' => $category, 'tag' => $tag])
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, RecipeRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page, $filters);
        self::assertSame($pagination, $result);
    }

    /**
     * Test getPaginatedList() with invalid filters.
     *
     * @throws NonUniqueResultException
     */
    public function testGetPaginatedListWithInvalidFilters(): void
    {
        $page = 3;
        $filters = ['category_id' => 100, 'tag_id' => 200];
        $pagination = $this->createMock(PaginationInterface::class);
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $this->categoryService
            ->expects(self::once())
            ->method('findOneById')
            ->with($filters['category_id'])
            ->willReturn(null);

        $this->tagService
            ->expects(self::once())
            ->method('findOneById')
            ->with($filters['tag_id'])
            ->willReturn(null);

        $this->recipeRepository
            ->expects(self::once())
            ->method('queryAll')
            ->with([])
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, RecipeRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page, $filters);
        self::assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $recipe = new Recipe();

        $this->recipeRepository
            ->expects(self::once())
            ->method('save')
            ->with($recipe);

        $this->service->save($recipe);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $recipe = new Recipe();

        $this->recipeRepository
            ->expects(self::once())
            ->method('delete')
            ->with($recipe);

        $this->service->delete($recipe);
    }

    /**
     * Test getById().
     */
    public function testGetById(): void
    {
        $recipe = new Recipe();
        $id = 42;

        $this->recipeRepository
            ->expects(self::once())
            ->method('find')
            ->with($id)
            ->willReturn($recipe);

        $result = $this->service->getById($id);
        self::assertSame($recipe, $result);
    }

    /**
     * Test getRecipeWithAssociations().
     *
     * @throws NonUniqueResultException
     */
    public function testGetRecipeWithAssociations(): void
    {
        $recipe = new Recipe();
        $id = 7;

        $this->recipeRepository
            ->expects(self::once())
            ->method('getRecipeWithAssociations')
            ->with($id)
            ->willReturn($recipe);

        $result = $this->service->getRecipeWithAssociations($id);
        self::assertSame($recipe, $result);
    }
}
