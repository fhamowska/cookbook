<?php

/**
 * Ingredient service.
 */

namespace App\Service;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use App\Repository\RecipeRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class IngredientService.
 */
class IngredientService implements IngredientServiceInterface
{
    /**
     * Constructor.
     *
     * @param IngredientRepository $ingredientRepository Ingredient repository
     * @param PaginatorInterface   $paginator            Paginator
     * @param RecipeRepository     $recipeRepository     Recipe repository
     */
    public function __construct(private readonly IngredientRepository $ingredientRepository, private readonly PaginatorInterface $paginator, private readonly RecipeRepository $recipeRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->ingredientRepository->queryAll(),
            $page,
            IngredientRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Ingredient $ingredient Ingredient entity
     */
    public function save(Ingredient $ingredient): void
    {
        $this->ingredientRepository->save($ingredient);
    }

    /**
     * Delete entity.
     *
     * @param Ingredient $ingredient Ingredient entity
     */
    public function delete(Ingredient $ingredient): void
    {
        $this->ingredientRepository->delete($ingredient);
    }

    /**
     * Find by title.
     *
     * @param string $title Ingredient title
     *
     * @return Ingredient|null Ingredient entity
     */
    public function findOneByTitle(string $title): ?Ingredient
    {
        return $this->ingredientRepository->findOneBy(['title' => $title]);
    }
}
