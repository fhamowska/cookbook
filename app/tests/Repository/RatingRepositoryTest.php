<?php

/*
 * Rating repository test.
 */

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RatingRepositoryTest.
 */
class RatingRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private RatingRepository $ratingRepository;

    /**
     * Helper to create a Category entity.
     *
     * @param string $title title
     *
     * @return Category Category
     */
    public function createCategory(string $title = 'Default Category'): Category
    {
        $category = new Category();
        $category->setTitle($title);
        $this->em->persist($category);
        $this->em->flush();

        return $category;
    }

    /**
     * Test saving a Rating and finding it by ID.
     */
    public function testSaveAndFind(): void
    {
        $category = $this->createCategory('Test Category');

        $recipe = new Recipe();
        $recipe->setTitle('Test Recipe');
        $recipe->setContent('Some content for test recipe');
        $recipe->setCategory($category);
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());

        $user = new User();
        $user->setEmail('test@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);

        $rating = new Rating();
        $rating->setRecipe($recipe);
        $rating->setAuthor($user);
        $rating->setValue(4);

        $this->em->persist($user);
        $this->em->persist($recipe);
        $this->ratingRepository->save($rating);

        $this->assertNotNull($rating->getId());
    }

    /**
     * Test deleting a Rating entity.
     */
    public function testDelete(): void
    {
        $category = $this->createCategory();

        $recipe = new Recipe();
        $recipe->setTitle('Recipe for delete test');
        $recipe->setContent('Content needed for delete test');
        $recipe->setCategory($category);
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $this->em->persist($recipe);

        $user = new User();
        $user->setEmail('author_for_delete@example.com');
        $user->setPassword('password');
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);

        $rating = new Rating();
        $rating->setValue(5);
        $rating->setRecipe($recipe);
        $rating->setAuthor($user);

        $this->em->persist($rating);
        $this->em->flush();

        $id = $rating->getId();
        $this->ratingRepository->delete($rating);

        $deleted = $this->ratingRepository->find($id);
        $this->assertNull($deleted);
    }

    /**
     * Test queryAll method returns a QueryBuilder and results array.
     */
    public function testQueryAll(): void
    {
        $qb = $this->ratingRepository->queryAll();
        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $qb);

        $results = $qb->getQuery()->getResult();
        $this->assertIsArray($results);
    }

    /**
     * Test queryByAuthor method filters Ratings by author User.
     */
    public function testQueryByAuthor(): void
    {
        $category = $this->createCategory();

        $user = new User();
        $user->setEmail('author@example.com');
        $user->setPassword('secret');
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);

        $recipe = new Recipe();
        $recipe->setTitle('Pizza');
        $recipe->setContent('Delicious pizza recipe');
        $recipe->setCategory($category);
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $this->em->persist($recipe);

        $rating = new Rating();
        $rating->setValue(3);
        $rating->setAuthor($user);
        $rating->setRecipe($recipe);
        $this->em->persist($rating);

        $this->em->flush();

        $qb = $this->ratingRepository->queryByAuthor($user);
        $results = $qb->getQuery()->getResult();

        $this->assertNotEmpty($results);
        $this->assertSame(3, $results[0]->getValue());
    }

    /**
     * Test calculating average rating value for a Recipe.
     */
    public function testCalculateAvg(): void
    {
        $category = $this->createCategory();

        $recipe = new Recipe();
        $recipe->setTitle('Cake');
        $recipe->setContent('Yummy cake recipe');
        $recipe->setCategory($category);
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());
        $this->em->persist($recipe);

        $user1 = new User();
        $user1->setEmail('user1@example.com');
        $user1->setPassword('pw');
        $user1->setRoles(['ROLE_USER']);
        $this->em->persist($user1);

        $user2 = new User();
        $user2->setEmail('user2@example.com');
        $user2->setPassword('pw');
        $user2->setRoles(['ROLE_USER']);
        $this->em->persist($user2);

        $rating1 = new Rating();
        $rating1->setRecipe($recipe);
        $rating1->setAuthor($user1);
        $rating1->setValue(4);
        $this->em->persist($rating1);

        $rating2 = new Rating();
        $rating2->setRecipe($recipe);
        $rating2->setAuthor($user2);
        $rating2->setValue(5);
        $this->em->persist($rating2);

        $this->em->flush();

        $avg = $this->ratingRepository->calculateAvg($recipe);

        $this->assertEquals(4.5, $avg);
    }

    /**
     * This method is called after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->ratingRepository = $this->em->getRepository(Rating::class);
    }
}
