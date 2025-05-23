<?php

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Rating;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class RatingFixtures.
 */
class RatingFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public function loadData(): void
    {
        if (!$this->manager instanceof \Doctrine\Persistence\ObjectManager || !$this->faker instanceof \Faker\Generator) {
            return;
        }

        $this->createMany(100, 'ratings', function () {
            $rating = new Rating();
            $rating->setValue($this->faker->numberBetween(1, 5));

            /** @var Recipe $recipe */
            $recipe = $this->getRandomReference('recipes', Recipe::class);
            $rating->setRecipe($recipe);

            /** @var User $author */
            $author = $this->getRandomReference('users', User::class);
            $rating->setAuthor($author);

            return $rating;
        });

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RecipeFixtures::class,
            UserFixtures::class,
        ];
    }
}
