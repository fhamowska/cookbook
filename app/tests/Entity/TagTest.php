<?php

/*
 * Tag entity test.
 */

namespace App\Tests\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

/**
 * TagTest class.
 */
class TagTest extends TestCase
{
    /**
     * Test that ID is initially null.
     */
    public function testIdIsInitiallyNull(): void
    {
        $tag = new Tag();
        $this->assertNull($tag->getId());
    }

    /**
     * Test setting and getting createdAt.
     */
    public function testCreatedAtCanBeSetAndRetrieved(): void
    {
        $date = new \DateTimeImmutable();
        $tag = new Tag();
        $tag->setCreatedAt($date);

        $this->assertSame($date, $tag->getCreatedAt());
    }

    /**
     * Test setting and getting updatedAt.
     */
    public function testUpdatedAtCanBeSetAndRetrieved(): void
    {
        $date = new \DateTimeImmutable();
        $tag = new Tag();
        $tag->setUpdatedAt($date);

        $this->assertSame($date, $tag->getUpdatedAt());
    }

    /**
     * Test setting and getting title.
     */
    public function testTitleCanBeSetAndRetrieved(): void
    {
        $tag = new Tag();
        $tag->setTitle('Vegetarian');

        $this->assertSame('Vegetarian', $tag->getTitle());
    }

    /**
     * Test that title can be set to null.
     */
    public function testTitleCanBeNull(): void
    {
        $tag = new Tag();
        $tag->setTitle(null);

        $this->assertNull($tag->getTitle());
    }
}
