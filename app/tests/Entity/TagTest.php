<?php

namespace App\Tests\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testIdIsInitiallyNull(): void
    {
        $tag = new Tag();
        $this->assertNull($tag->getId());
    }

    public function testCreatedAtCanBeSetAndRetrieved(): void
    {
        $date = new \DateTimeImmutable();
        $tag = new Tag();
        $tag->setCreatedAt($date);

        $this->assertSame($date, $tag->getCreatedAt());
    }

    public function testUpdatedAtCanBeSetAndRetrieved(): void
    {
        $date = new \DateTimeImmutable();
        $tag = new Tag();
        $tag->setUpdatedAt($date);

        $this->assertSame($date, $tag->getUpdatedAt());
    }

    public function testTitleCanBeSetAndRetrieved(): void
    {
        $tag = new Tag();
        $tag->setTitle('Vegetarian');

        $this->assertSame('Vegetarian', $tag->getTitle());
    }

    public function testTitleCanBeNull(): void
    {
        $tag = new Tag();
        $tag->setTitle(null);

        $this->assertNull($tag->getTitle());
    }
}
