<?php

namespace App\Tests\Repository;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class RecipeRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private RecipeRepository $recipeRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->recipeRepository = $this->em->getRepository(Recipe::class);
    }

    private function createRecipe(
        string $title,
        string $content,
        Category $category,
        array $tags = []
    ): Recipe {
        $recipe = new Recipe();
        $recipe->setTitle($title);
        $recipe->setContent($content);
        $recipe->setCategory($category);
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());

        foreach ($tags as $tag) {
            $recipe->addTag($tag);
        }

        $this->em->persist($recipe);

        return $recipe;
    }

    public function testSaveAndDelete(): void
    {
        $category = new Category();
        $category->setTitle('Desserts');
        $this->em->persist($category);

        $recipe = $this->createRecipe('Chocolate Cake', 'Delicious chocolate cake recipe', $category);

        $this->recipeRepository->save($recipe);

        $this->assertNotNull($recipe->getId());

        $id = $recipe->getId();
        $this->recipeRepository->delete($recipe);

        $this->assertNull($this->recipeRepository->find($id));
    }

    public function testQueryAllWithCategoryFilter(): void
    {
        $category = new Category();
        $category->setTitle('Main Course');
        $this->em->persist($category);

        $recipe = $this->createRecipe('Grilled Chicken', 'Grilled chicken recipe content', $category);

        $this->em->flush();

        $qb = $this->recipeRepository->queryAll(['category' => $category]);
        $results = $qb->getQuery()->getResult();

        $this->assertNotEmpty($results);
        $this->assertInstanceOf(Recipe::class, $results[0]);
    }

    public function testQueryAllWithTagFilter(): void
    {
        $tag = new Tag();
        $tag->setTitle('Spicy');
        $this->em->persist($tag);

        $category = new Category();
        $category->setTitle('Sides');
        $this->em->persist($category);

        $recipe = $this->createRecipe('Spicy Fries', 'Crispy and spicy fries recipe', $category, [$tag]);

        $this->em->flush();

        $qb = $this->recipeRepository->queryAll(['tag' => $tag]);
        $results = $qb->getQuery()->getResult();

        $this->assertNotEmpty($results);
        $this->assertTrue($results[0]->getTags()->contains($tag));
    }

    public function testGetRecipeWithAssociations(): void
    {
        $category = new Category();
        $category->setTitle('Breakfast');
        $this->em->persist($category);

        $recipe = $this->createRecipe('Pancakes', 'Fluffy pancakes recipe', $category);

        $this->em->flush();

        $fetchedRecipe = $this->recipeRepository->getRecipeWithAssociations($recipe->getId());
        $this->assertInstanceOf(Recipe::class, $fetchedRecipe);
        $this->assertEquals('Pancakes', $fetchedRecipe->getTitle());
    }

    public function testCountByCategory(): void
    {
        $category = new Category();
        $category->setTitle('Soups');
        $this->em->persist($category);

        for ($i = 0; $i < 2; $i++) {
            $this->createRecipe("Soup $i", "Delicious soup number $i", $category);
        }

        $this->em->flush();

        $count = $this->recipeRepository->countByCategory($category);
        $this->assertEquals(2, $count);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
