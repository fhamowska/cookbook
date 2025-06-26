<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Ingredient;
use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    public function testIdIsInitiallyNull(): void
    {
        $recipe = new Recipe();
        $this->assertNull($recipe->getId());
    }

    public function testCreatedAtCanBeSetAndRetrieved(): void
    {
        $date = new \DateTimeImmutable();
        $recipe = new Recipe();
        $recipe->setCreatedAt($date);

        $this->assertSame($date, $recipe->getCreatedAt());
    }

    public function testUpdatedAtCanBeSetAndRetrieved(): void
    {
        $date = new \DateTimeImmutable();
        $recipe = new Recipe();
        $recipe->setUpdatedAt($date);

        $this->assertSame($date, $recipe->getUpdatedAt());
    }

    public function testTitleCanBeSetAndRetrieved(): void
    {
        $recipe = new Recipe();
        $recipe->setTitle('Delicious Lasagna');

        $this->assertSame('Delicious Lasagna', $recipe->getTitle());
    }

    public function testContentCanBeSetAndRetrieved(): void
    {
        $recipe = new Recipe();
        $recipe->setContent('Step 1: Do this...');

        $this->assertSame('Step 1: Do this...', $recipe->getContent());
    }

    public function testCategoryCanBeSetAndRetrieved(): void
    {
        $category = new Category();
        $recipe = new Recipe();
        $recipe->setCategory($category);

        $this->assertSame($category, $recipe->getCategory());
    }

    public function testTagsCanBeAddedAndRemoved(): void
    {
        $recipe = new Recipe();
        $tag = new Tag();

        $recipe->addTag($tag);
        $this->assertTrue($recipe->getTags()->contains($tag));

        $recipe->removeTag($tag);
        $this->assertFalse($recipe->getTags()->contains($tag));
    }

    public function testIngredientsCanBeAddedAndRemoved(): void
    {
        $recipe = new Recipe();
        $ingredient = new Ingredient();

        $recipe->addIngredient($ingredient);
        $this->assertTrue($recipe->getIngredients()->contains($ingredient));

        $recipe->removeIngredient($ingredient);
        $this->assertFalse($recipe->getIngredients()->contains($ingredient));
    }

    public function testCommentsCanBeAddedAndRemoved(): void
    {
        $recipe = new Recipe();
        $comment = new Comment();

        $recipe->addComment($comment);
        $this->assertTrue($recipe->getComments()->contains($comment));
        $this->assertSame($recipe, $comment->getRecipe());

        $recipe->removeComment($comment);
        $this->assertFalse($recipe->getComments()->contains($comment));
        $this->assertNull($comment->getRecipe());
    }

    public function testRatingsCanBeAddedAndRemoved(): void
    {
        $recipe = new Recipe();
        $rating = new Rating();

        $recipe->addRating($rating);
        $this->assertTrue($recipe->getRatings()->contains($rating));
        $this->assertSame($recipe, $rating->getRecipe());

        $recipe->removeRating($rating);
        $this->assertFalse($recipe->getRatings()->contains($rating));
        $this->assertNull($rating->getRecipe());
    }

    public function testAverageRatingCanBeSetAndRetrieved(): void
    {
        $recipe = new Recipe();
        $recipe->setAverageRating(4.5);

        $this->assertSame(4.5, $recipe->getAverageRating());
    }

    public function testInitialCollectionsAreEmpty(): void
    {
        $recipe = new Recipe();

        $this->assertCount(0, $recipe->getTags());
        $this->assertCount(0, $recipe->getIngredients());
        $this->assertCount(0, $recipe->getComments());
        $this->assertCount(0, $recipe->getRatings());
    }
}
