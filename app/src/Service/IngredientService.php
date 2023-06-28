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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

/**
 * Class IngredientService.
 */
class IngredientService implements IngredientServiceInterface
{
    private IngredientRepository $ingredientRepository;
    private RecipeRepository $recipeRepository;
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param IngredientRepository $ingredientRepository Ingredient repository
     * @param PaginatorInterface   $paginator            Paginator
     * @param RecipeRepository     $recipeRepository     Recipe repository
     */
    public function __construct(IngredientRepository $ingredientRepository, PaginatorInterface $paginator, RecipeRepository $recipeRepository)
    {
        $this->ingredientRepository = $ingredientRepository;
        $this->paginator = $paginator;
        $this->recipeRepository = $recipeRepository;
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
     * Can Ingredient be deleted?
     *
     * @param Ingredient $ingredient Ingredient entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Ingredient $ingredient): bool
    {
        try {
            $result = $this->recipeRepository->countByIngredient($ingredient);

            return !($result > 0);
        } catch (NoResultException|NonUniqueResultException) {
            return false;
        }
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
        return $this->ingredientRepository->findOneByTitle($title);
    }
}
