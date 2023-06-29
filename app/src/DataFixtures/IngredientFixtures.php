<?php
/**
 * Ingredient fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Ingredient;

/**
 * Class IngredientFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class IngredientFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     *
     * @psalm-suppress PossiblyNullReference
     * @psalm-suppress UnusedClosureParam
     */
    public function loadData(): void
    {
        $this->createMany(10, 'ingredients', function () {
            $ingredient = new Ingredient();
            $ingredient->setTitle($this->faker->unique()->word);
            $ingredient->setCreatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );
            $ingredient->setUpdatedAt(
                \DateTimeImmutable::createFromMutable(
                    $this->faker->dateTimeBetween('-100 days', '-1 days')
                )
            );

            return $ingredient;
        });

        $this->manager->flush();
    }
}
