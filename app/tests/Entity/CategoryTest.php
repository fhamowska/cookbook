<?php

/**
 * Category entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

/**
 * Class CategoryTest.
 *
 * Unit tests for the Category entity.
 */
class CategoryTest extends TestCase
{
    /**
     * Test setting and getting the title.
     */
    public function testCanSetAndGetTitle(): void
    {
        $category = new Category();
        $category->setTitle('Desserts');

        $this->assertSame('Desserts', $category->getTitle());
    }

    /**
     * Test setting and getting the createdAt timestamp.
     */
    public function testCanSetAndGetCreatedAt(): void
    {
        $now = new \DateTimeImmutable();
        $category = new Category();
        $category->setCreatedAt($now);

        $this->assertSame($now, $category->getCreatedAt());
    }

    /**
     * Test setting and getting the updatedAt timestamp.
     */
    public function testCanSetAndGetUpdatedAt(): void
    {
        $now = new \DateTimeImmutable();
        $category = new Category();
        $category->setUpdatedAt($now);

        $this->assertSame($now, $category->getUpdatedAt());
    }

    /**
     * Test that getId() initially returns null.
     */
    public function testGetIdInitiallyReturnsNull(): void
    {
        $category = new Category();

        $this->assertNull($category->getId());
    }
}
