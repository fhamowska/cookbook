<?php

namespace App\Tests\Repository;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class IngredientRepositoryTest.
 */
class IngredientRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private IngredientRepository $ingredientRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();

        $this->ingredientRepository = $this->entityManager->getRepository(Ingredient::class);
    }

    public function testSave(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setTitle('Flour');
        $ingredient->setCreatedAt(new \DateTimeImmutable());
        $ingredient->setUpdatedAt(new \DateTimeImmutable());

        $this->ingredientRepository->save($ingredient);
        $id = $ingredient->getId();

        $this->assertNotNull($id);
        $fetched = $this->ingredientRepository->find($id);
        $this->assertInstanceOf(Ingredient::class, $fetched);
        $this->assertSame('Flour', $fetched->getTitle());
    }

    public function testDelete(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setTitle('Salt');
        $ingredient->setCreatedAt(new \DateTimeImmutable());
        $ingredient->setUpdatedAt(new \DateTimeImmutable());

        $this->ingredientRepository->save($ingredient);
        $id = $ingredient->getId();

        $this->ingredientRepository->delete($ingredient);

        $this->assertNull($this->ingredientRepository->find($id));
    }

    public function testQueryAll(): void
    {
        $qb = $this->ingredientRepository->queryAll();

        $this->assertInstanceOf(\Doctrine\ORM\QueryBuilder::class, $qb);

        $results = $qb->getQuery()->getResult();

        $this->assertIsArray($results);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        unset($this->ingredientRepository);
    }
}
