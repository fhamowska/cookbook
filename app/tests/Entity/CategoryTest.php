<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function testCanSetAndGetTitle(): void
    {
        $category = new Category();
        $category->setTitle('Desserts');

        $this->assertSame('Desserts', $category->getTitle());
    }

    public function testCanSetAndGetCreatedAt(): void
    {
        $now = new \DateTimeImmutable();
        $category = new Category();
        $category->setCreatedAt($now);

        $this->assertSame($now, $category->getCreatedAt());
    }

    public function testCanSetAndGetUpdatedAt(): void
    {
        $now = new \DateTimeImmutable();
        $category = new Category();
        $category->setUpdatedAt($now);

        $this->assertSame($now, $category->getUpdatedAt());
    }

    public function testGetIdInitiallyReturnsNull(): void
    {
        $category = new Category();

        $this->assertNull($category->getId());
    }
}
