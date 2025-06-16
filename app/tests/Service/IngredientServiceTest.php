<?php

/*
 * Ingredient service test.
 */

namespace App\Tests\Service;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use App\Service\IngredientService;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * Class IngredientServiceTest.
 */
class IngredientServiceTest extends TestCase
{
    private IngredientRepository $ingredientRepository;
    private PaginatorInterface $paginator;
    private IngredientService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->ingredientRepository = $this->createMock(IngredientRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $recipeRepository = $this->createMock(RecipeRepository::class);

        $this->service = new IngredientService(
            $this->ingredientRepository,
            $this->paginator,
            $recipeRepository
        );
    }

    /**
     * Test getPaginatedList().
     */
    public function testGetPaginatedList(): void
    {
        $page = 1;
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $pagination = $this->createMock(PaginationInterface::class);

        $this->ingredientRepository
            ->expects(self::once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, IngredientRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page);
        $this->assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $ingredient = new Ingredient();

        $this->ingredientRepository
            ->expects(self::once())
            ->method('save')
            ->with($ingredient);

        $this->service->save($ingredient);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $ingredient = new Ingredient();

        $this->ingredientRepository
            ->expects(self::once())
            ->method('delete')
            ->with($ingredient);

        $this->service->delete($ingredient);
    }

    /**
     * Test findOneByTitle().
     */
    public function testFindOneByTitle(): void
    {
        $title = 'Sugar';
        $ingredient = new Ingredient();

        $this->ingredientRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['title' => $title])
            ->willReturn($ingredient);

        $result = $this->service->findOneByTitle($title);
        $this->assertSame($ingredient, $result);
    }
}
