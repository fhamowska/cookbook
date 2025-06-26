<?php

namespace App\Tests\Entity;

use App\Entity\Rating;
use App\Entity\Recipe;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class RatingTest extends TestCase
{
    public function testSetAndGetValue(): void
    {
        $rating = new Rating();
        $rating->setValue(4);

        $this->assertSame(4, $rating->getValue());
    }

    public function testSetAndGetRecipe(): void
    {
        $recipe = new Recipe();
        $rating = new Rating();
        $rating->setRecipe($recipe);

        $this->assertSame($recipe, $rating->getRecipe());
    }

    public function testSetAndGetAuthor(): void
    {
        $user = new User();
        $rating = new Rating();
        $rating->setAuthor($user);

        $this->assertSame($user, $rating->getAuthor());
    }

    public function testInitialIdIsNull(): void
    {
        $rating = new Rating();

        $this->assertNull($rating->getId());
    }
}
