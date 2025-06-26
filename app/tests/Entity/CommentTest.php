<?php

/*
 * Ingredient entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Recipe;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentTest.
 */
class CommentTest extends TestCase
{
    /**
     * Test content setter and getter.
     */
    public function testContentCanBeSetAndRetrieved(): void
    {
        $comment = new Comment();
        $comment->setContent('Test comment content');

        $this->assertSame('Test comment content', $comment->getContent());
    }

    /**
     * Test author setter and getter.
     */
    public function testAuthorCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $comment = new Comment();
        $comment->setAuthor($user);

        $this->assertSame($user, $comment->getAuthor());
    }

    /**
     * Test recipe setter and getter.
     */
    public function testRecipeCanBeSetAndRetrieved(): void
    {
        $recipe = new Recipe();
        $comment = new Comment();
        $comment->setRecipe($recipe);

        $this->assertSame($recipe, $comment->getRecipe());
    }

    /**
     * Test initial ID is null.
     */
    public function testGetIdReturnsNullInitially(): void
    {
        $comment = new Comment();
        $this->assertNull($comment->getId());
    }
}
