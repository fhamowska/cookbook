<?php

namespace App\Tests\Repository;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class TagRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private TagRepository $tagRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get('doctrine')->getManager();
        $this->tagRepository = $this->em->getRepository(Tag::class);
    }

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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
    }
}
