<?php

/**
 * Recipe fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Recipe;
use App\Entity\Tag;
use App\Entity\Ingredient;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class RecipeFixtures.
 */
class RecipeFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof \Doctrine\Persistence\ObjectManager || !$this->faker instanceof \Faker\Generator) {
            return;
        }

        $this->createMany(20, 'recipes', function () {
            $recipe = new Recipe();
            $recipe->setTitle($this->faker->sentence);
            $recipe->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $recipe->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            /** @var Category $category */
            $category = $this->getRandomReference('categories', Category::class);
            $recipe->setCategory($category);

            $recipe->setContent($this->faker->paragraph(15));
            $recipe->setAverageRating(0);

            /** @var Tag[] $tags */
            $tags = $this->getRandomReferenceList(
                'tags',
                Tag::class,
                $this->faker->numberBetween(1, 5)
            );
            foreach ($tags as $tag) {
                $recipe->addTag($tag);
            }

            /** @var Ingredient[] $ingredients */
            $ingredients = $this->getRandomReferenceList(
                'ingredients',
                Ingredient::class,
                $this->faker->numberBetween(3, 10)
            );
            foreach ($ingredients as $ingredient) {
                $recipe->addIngredient($ingredient);
            }

            return $recipe;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixture classes
     * on which this class depends.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
            TagFixtures::class,
            IngredientFixtures::class,
        ];
    }
}
