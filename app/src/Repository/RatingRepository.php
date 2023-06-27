<?php
/**
 * Rating repository.
 */

namespace App\Repository;

use App\Entity\Rating;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    /**
     * Items per page.
     *
     * @constant int
     */
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    /**
     * Query all records.
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder()
            ->select('rating', 'partial recipe.{id}')
            ->join('rating.recipe', 'recipe');
    }

    /**
     * Save entity.
     *
     * @param Rating $rating Rating entity
     */
    public function save(Rating $rating): void
    {
        $this->_em->persist($rating);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Rating $rating Rating entity
     */
    public function delete(Rating $rating): void
    {
        $this->_em->remove($rating);
        $this->_em->flush();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('rating');
    }

    /**
     * Query ratings by author.
     *
     * @param User $user User entity
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(User $user): QueryBuilder
    {
        $queryBuilder = $this->queryAll();

        $queryBuilder->andWhere('rating.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }
}