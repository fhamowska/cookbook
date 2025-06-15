<?php

/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

/**
 * Class CommentFixtures.
 *
 * @psalm-suppress MissingConstructor
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Load data.
     */
    public function loadData(): void
    {
        if (!$this->manager instanceof ObjectManager || !$this->faker instanceof Generator) {
            return;
        }

        $this->createMany(100, 'comments', function () {
            $comment = new Comment();
            $comment->setContent($this->faker->text);

            /** @var Recipe $recipe */
            $recipe = $this->getRandomReference('recipes', Recipe::class);
            $comment->setRecipe($recipe);

            /** @var User $author */
            $author = $this->getRandomReference('users', User::class);
            $comment->setAuthor($author);

            return $comment;
        });

        $this->manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return array<class-string>
     */
    public function getDependencies(): array
    {
        return [
            RecipeFixtures::class,
            UserFixtures::class,
        ];
    }
}
