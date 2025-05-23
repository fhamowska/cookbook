<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    public function loadData(): void
    {
        if (!$this->manager instanceof \Doctrine\Persistence\ObjectManager || !$this->faker instanceof \Faker\Generator) {
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

    public function getDependencies(): array
    {
        return [
            RecipeFixtures::class,
            UserFixtures::class,
        ];
    }
}
