<?php

/*
 * Comment repository test.
 */

namespace App\Tests\Repository;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Recipe;
use App\Entity\Category;
use App\Repository\CommentRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CommentRepositoryTest.
 */
class CommentRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private CommentRepository $repo;

    /**
     * Setup before each test.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->repo = $this->em->getRepository(Comment::class);
    }

    /**
     * Test saving a Comment entity.
     */
    public function testSave(): void
    {
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('password');
        $this->em->persist($user);

        $category = new Category();
        $category->setTitle('Test Category');
        $this->em->persist($category);

        $recipe = new Recipe();
        $recipe->setTitle('Test Recipe');
        $recipe->setContent('Test content');
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $recipe->setCategory($category);
        $this->em->persist($recipe);

        $this->em->flush();

        $comment = new Comment();
        $comment->setContent('Hello');
        $comment->setAuthor($user);
        $comment->setRecipe($recipe);

        $this->repo->save($comment);
        $this->assertNotNull($comment->getId());
        $this->assertInstanceOf(Comment::class, $this->repo->find($comment->getId()));
    }

    /**
     * Test deleting a Comment entity.
     */
    public function testDelete(): void
    {
        $user = new User();
        $user->setEmail('deleteuser@example.com');
        $user->setPassword('password');
        $this->em->persist($user);

        $category = new Category();
        $category->setTitle('Delete Category');
        $this->em->persist($category);

        $recipe = new Recipe();
        $recipe->setTitle('Recipe to delete comment');
        $recipe->setContent('Test content');
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $recipe->setCategory($category);
        $this->em->persist($recipe);

        $this->em->flush();

        $comment = new Comment();
        $comment->setContent('To delete');
        $comment->setAuthor($user);
        $comment->setRecipe($recipe);
        $this->repo->save($comment);
        $id = $comment->getId();

        $this->repo->delete($comment);
        $this->assertNull($this->repo->find($id));
    }

    /**
     * Test queryAll method returns a QueryBuilder instance with proper joins.
     */
    public function testQueryAll(): void
    {
        $qb = $this->repo->queryAll();

        $this->assertInstanceOf(QueryBuilder::class, $qb);

        $dql = $qb->getDQL();
        $this->assertStringContainsString('LEFT JOIN', $dql);
        $this->assertStringContainsString('comment.author', $dql);
        $this->assertStringContainsString('comment.recipe', $dql);

        $results = $qb->getQuery()->getResult();
        $this->assertIsArray($results);
    }

    /**
     * Test queryByAuthor returns comments only for the specified author.
     */
    public function testQueryByAuthor(): void
    {
        $user = new User();
        $user->setEmail('author@example.com');
        $user->setPassword('123456');
        $this->em->persist($user);

        $category = new Category();
        $category->setTitle('Author Category');
        $this->em->persist($category);

        $recipe = new Recipe();
        $recipe->setTitle('Author Recipe');
        $recipe->setContent('Test content');
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $recipe->setCategory($category);
        $this->em->persist($recipe);

        $this->em->flush();

        $comment = new Comment();
        $comment->setContent('Authored Comment');
        $comment->setAuthor($user);
        $comment->setRecipe($recipe);
        $this->repo->save($comment);

        $qb = $this->repo->queryByAuthor($user);

        $this->assertInstanceOf(QueryBuilder::class, $qb);
        $results = $qb->getQuery()->getResult();

        $this->assertNotEmpty($results);
        foreach ($results as $result) {
            $this->assertSame($user->getEmail(), $result->getAuthor()->getEmail());
        }
    }

    /**
     * Test the private method getOrCreateQueryBuilder.
     */
    public function testGetOrCreateQueryBuilder(): void
    {
        $qb = (new \ReflectionMethod($this->repo, 'getOrCreateQueryBuilder'))->invoke($this->repo, null);
        $this->assertInstanceOf(QueryBuilder::class, $qb);

        $existingQb = $this->em->createQueryBuilder()->select('1');
        $returnedQb = (new \ReflectionMethod($this->repo, 'getOrCreateQueryBuilder'))->invoke($this->repo, $existingQb);
        $this->assertSame($existingQb, $returnedQb);
    }

    /**
     * Test constant PAGINATOR_ITEMS_PER_PAGE is set to 10.
     */
    public function testPaginatorItemsPerPageConstant(): void
    {
        $this->assertSame(10, CommentRepository::PAGINATOR_ITEMS_PER_PAGE);
    }

    /**
     * Tear down entity manager after tests.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
