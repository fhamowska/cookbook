<?php

/**
 * Rating service.
 */

namespace App\Service;

use App\Entity\Rating;
use App\Entity\User;
use App\Repository\RatingRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class RatingService.
 */
class RatingService implements RatingServiceInterface
{
    /**
     * Rating repository.
     */
    private RatingRepository $ratingRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param RatingRepository   $ratingRepository Rating repository
     * @param PaginatorInterface $paginator        Paginator
     */
    public function __construct(RatingRepository $ratingRepository, PaginatorInterface $paginator)
    {
        $this->ratingRepository = $ratingRepository;
        $this->paginator = $paginator;
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
}
