<?php
/**
 * Rating fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Recipe;
use App\Entity\Rating;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class RatingFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class RatingFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullPropertyFetch
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(100, 'ratings', function (int $i) {
            $rating = new Rating();
            $rating->setValue($this->faker->numberBetween(1, 5));

            /** @var Recipe recipe */
            $recipe = $this->getRandomReference('recipes');
            $rating->setRecipe($recipe);

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $rating->setAuthor($author);

            return $rating;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[] of dependencies
     *
     * @psalm-return array{0: CategoryFixtures::class}
     */
    public function getDependencies(): array
    {
        return [RecipeFixtures::class];
    }
}
