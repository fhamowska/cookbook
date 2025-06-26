<?php

/*
 * Tag repository test.
 */

namespace App\Tests\Repository;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TagRepositoryTest.
 */
class TagRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private TagRepository $tagRepository;

    /**
     * Set up test environment.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->tagRepository = $this->em->getRepository(Tag::class);
    }

    /**
     * Test saving a Tag and deleting it.
     */
    public function testSaveAndDelete(): void
    {
        $tag = new Tag();
        $tag->setTitle('Test Tag');
        $tag->setCreatedAt(new \DateTimeImmutable());
        $tag->setUpdatedAt(new \DateTimeImmutable());

        $this->tagRepository->save($tag);
        $this->assertNotNull($tag->getId());

        $id = $tag->getId();
        $this->tagRepository->delete($tag);

        $this->assertNull($this->tagRepository->find($id));
    }

    /**
     * Test that queryAll method returns Tag entities.
     */
    public function testQueryAllReturnsTags(): void
    {
        $tag = new Tag();
        $tag->setTitle('Query Tag');
        $tag->setCreatedAt(new \DateTimeImmutable());
        $tag->setUpdatedAt(new \DateTimeImmutable());
        $this->tagRepository->save($tag);

        $qb = $this->tagRepository->queryAll();
        $tags = $qb->getQuery()->getResult();

        $this->assertIsArray($tags);
        $this->assertNotEmpty($tags);
        $this->assertInstanceOf(Tag::class, $tags[0]);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
