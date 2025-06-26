<?php

/*
 * Category repository test.
 */

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CategoryRepositoryTest.
 */
class CategoryRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private CategoryRepository $repository;

    /**
     * Boot kernel and initialize entity manager and repository.
     */
    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Category::class);
    }

    /**
     * Test saving and finding a Category entity.
     */
    public function testSaveAndFind(): void
    {
        $category = new Category();
        $category->setTitle('Test Category');
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());

        $this->repository->save($category);
        $id = $category->getId();

        $found = $this->repository->find($id);

        $this->assertInstanceOf(Category::class, $found);
        $this->assertSame('Test Category', $found->getTitle());
    }

    /**
     * Test deleting a Category entity.
     */
    public function testDelete(): void
    {
        $category = new Category();
        $category->setTitle('To Delete');
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());

        $this->repository->save($category);
        $id = $category->getId();

        $this->repository->delete($category);

        $found = $this->repository->find($id);

        $this->assertNull($found);
    }

    /**
     * Test querying all Category entities.
     */
    public function testQueryAll(): void
    {
        $category = new Category();
        $category->setTitle('Query Test');
        $category->setCreatedAt(new \DateTimeImmutable());
        $category->setUpdatedAt(new \DateTimeImmutable());
        $this->repository->save($category);

        $queryBuilder = $this->repository->queryAll();

        $result = $queryBuilder->getQuery()->getResult();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Category::class, $result[0]);
    }

    /**
     * Close entity manager after tests.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
