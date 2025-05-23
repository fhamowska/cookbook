<?php

/**
 * Recipe service interface.
 */

namespace App\Service;

use App\Entity\Recipe;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface RecipeServiceInterface.
 */
interface RecipeServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function save(Recipe $recipe): void;

    /**
     * Delete entity.
     *
     * @param Recipe $recipe Recipe entity
     */
    public function delete(Recipe $recipe): void;

    /**
     * Get recipe by id.
     *
     * @param int $id Recipe id
     */
    public function getById(int $id): ?Recipe;

    /**
     * Get recipe with its associated entities.
     *
     * @param int $id Id
     *
     * @return Recipe|null Recipe
     */
    public function getRecipeWithAssociations(int $id): ?Recipe;
}
