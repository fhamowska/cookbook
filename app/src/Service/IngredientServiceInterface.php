<?php
/**
 * Ingredient service interface.
 */

namespace App\Service;

use App\Entity\Ingredient;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface IngredientServiceInterface.
 */
interface IngredientServiceInterface
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
     * @param Ingredient $ingredient Ingredient entity
     */
    public function save(Ingredient $ingredient): void;

    /**
     * Delete entity.
     *
     * @param Ingredient $ingredient Ingredient entity
     */
    public function delete(Ingredient $ingredient): void;

    /**
     * Can Ingredient be deleted?
     *
     * @param Ingredient $ingredient Ingredient entity
     *
     * @return bool Result
     */
    public function canBeDeleted(Ingredient $ingredient): bool;

    /**
     * Find by title.
     *
     * @param string $title Ingredient title
     *
     * @return Ingredient|null Ingredient entity
     */
    public function findOneByTitle(string $title): ?Ingredient;
}
