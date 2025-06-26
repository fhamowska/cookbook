<?php

/*
 * Rating entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class RatingTest.
 */
class RatingTest extends TestCase
{
    /**
     * Test setting and getting the value.
     */
    public function testSetAndGetValue(): void
    {
        $rating = new Rating();
        $rating->setValue(4);

        $this->assertSame(4, $rating->getValue());
    }

    /**
     * Test setting and getting the recipe.
     */
    public function testSetAndGetRecipe(): void
    {
        $recipe = new Recipe();
        $rating = new Rating();
        $rating->setRecipe($recipe);

        $this->assertSame($recipe, $rating->getRecipe());
    }

    /**
     * Test setting and getting the author.
     */
    public function testSetAndGetAuthor(): void
    {
        $user = new User();
        $rating = new Rating();
        $rating->setAuthor($user);

        $this->assertSame($user, $rating->getAuthor());
    }

    /**
     * Test that initial ID is null.
     */
    public function testInitialIdIsNull(): void
    {
        $rating = new Rating();

        $this->assertNull($rating->getId());
    }
}
