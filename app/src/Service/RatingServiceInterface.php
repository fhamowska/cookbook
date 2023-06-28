<?php
/**
 * Rating service interface.
 */

namespace App\Service;

use App\Entity\Rating;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Class RatingServiceInterface.
 */
interface RatingServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author User
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     */
    public function save(Rating $rating): void;

    /**
     * Delete entity.
     *
     * @param Rating $rating Rating entity
     */
    public function delete(Rating $rating): void;
}
