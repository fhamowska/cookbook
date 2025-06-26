<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Recipe;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testContentCanBeSetAndRetrieved(): void
    {
        $comment = new Comment();
        $comment->setContent('Test comment content');

        $this->assertSame('Test comment content', $comment->getContent());
    }

    public function testAuthorCanBeSetAndRetrieved(): void
    {
        $user = new User();
        $comment = new Comment();
        $comment->setAuthor($user);

        $this->assertSame($user, $comment->getAuthor());
    }

    public function testRecipeCanBeSetAndRetrieved(): void
    {
        $recipe = new Recipe();
        $comment = new Comment();
        $comment->setRecipe($recipe);

        $this->assertSame($recipe, $comment->getRecipe());
    }

    public function testGetIdReturnsNullInitially(): void
    {
        $comment = new Comment();
        $this->assertNull($comment->getId());
    }
}
