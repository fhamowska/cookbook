<?php
/**
 * User service.
 */

namespace App\Service;

use App\Repository\RecipeRepository;
use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * User repository.
     */
    private UserRepository $userRepository;

    /**
     * Recipe repository.
     */
    private RecipeRepository $recipeRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param UserRepository     $userRepository  User repository
     * @param PaginatorInterface $paginator       Paginator
     * @param RecipeRepository    $recipeRepository Recipe repository
     */
    public function __construct(UserRepository $userRepository, PaginatorInterface $paginator, RecipeRepository $recipeRepository)
    {
        $this->userRepository = $userRepository;
        $this->paginator = $paginator;
        $this->recipeRepository = $recipeRepository;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->userRepository->save($user);
    }

    /**
     * Find by id.
     *
     * @param int $id User id
     *
     * @return User|null User entity
     *
     * @throws NonUniqueResultException
     */
    public function findOneById(int $id): ?User
    {
        return $this->userRepository->findOneById($id);
    }
}