<?php

namespace App\Tests\Entity;

use App\Entity\Ingredient;
use PHPUnit\Framework\TestCase;

class IngredientTest extends TestCase
{
    public function testSetAndGetTitle(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setTitle('Salt');

        $this->assertSame('Salt', $ingredient->getTitle());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $ingredient = new Ingredient();
        $date = new \DateTimeImmutable('2024-01-01 12:00:00');
        $ingredient->setCreatedAt($date);

        $this->assertSame($date, $ingredient->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $ingredient = new Ingredient();
        $date = new \DateTimeImmutable('2024-02-01 12:00:00');
        $ingredient->setUpdatedAt($date);

        $this->assertSame($date, $ingredient->getUpdatedAt());
    }

    public function testInitialIdIsNull(): void
    {
        $ingredient = new Ingredient();

        $this->assertNull($ingredient->getId());
    }
}
