<?php

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

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->repository = $this->entityManager->getRepository(Category::class);
    }

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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
