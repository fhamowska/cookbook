<?php

/*
 * Ingredient entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Ingredient;
use PHPUnit\Framework\TestCase;

/**
 * Class IngredientTest.
 */
class IngredientTest extends TestCase
{
    /**
     * Test setting and getting the title.
     */
    public function testSetAndGetTitle(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setTitle('Salt');

        $this->assertSame('Salt', $ingredient->getTitle());
    }

    /**
     * Test setting and getting the createdAt value.
     */
    public function testSetAndGetCreatedAt(): void
    {
        $ingredient = new Ingredient();
        $date = new \DateTimeImmutable('2024-01-01 12:00:00');
        $ingredient->setCreatedAt($date);

        $this->assertSame($date, $ingredient->getCreatedAt());
    }

    /**
     * Test setting and getting the updatedAt value.
     */
    public function testSetAndGetUpdatedAt(): void
    {
        $ingredient = new Ingredient();
        $date = new \DateTimeImmutable('2024-02-01 12:00:00');
        $ingredient->setUpdatedAt($date);

        $this->assertSame($date, $ingredient->getUpdatedAt());
    }

    /**
     * Test that initial ID is null.
     */
    public function testInitialIdIsNull(): void
    {
        $ingredient = new Ingredient();

        $this->assertNull($ingredient->getId());
    }
}
