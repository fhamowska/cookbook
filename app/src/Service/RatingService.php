<?php

/**
 * Rating service.
 */

namespace App\Service;

use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RatingRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RatingService.
 */
class RatingService implements RatingServiceInterface
{
    /**
     * Constructor.
     *
     * @param RatingRepository   $ratingRepository Rating repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(private readonly RatingRepository $ratingRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int  $page   Page number
     * @param User $author Author
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface
    {
        if ($author->hasRole('ROLE_ADMIN')) {
            return $this->paginator->paginate(
                $this->ratingRepository->queryAll(),
                $page,
                RatingRepository::PAGINATOR_ITEMS_PER_PAGE
            );
        }

        return $this->paginator->paginate(
            $this->ratingRepository->queryByAuthor($author),
            $page,
            RatingRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     */
    public function save(Rating $rating): void
    {
        $this->ratingRepository->save($rating);
    }

    /**
     * Delete entity.
     *
     * @param Rating $rating Rating entity
     */
    public function delete(Rating $rating): void
    {
        $this->ratingRepository->delete($rating);
    }

    /**
     * Calculate the average rating for a recipe.
     *
     * @param Recipe $recipe The recipe for which to calculate the average rating
     *
     * @return float The average rating for the recipe
     *
     * @throws NonUniqueResultException
     */
    public function calculateAvg(Recipe $recipe): float
    {
        return $this->ratingRepository->calculateAvg($recipe);
    }
}
