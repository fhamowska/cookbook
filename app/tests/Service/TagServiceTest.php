<?php

/*
 * Tag service test.
 */

namespace App\Tests\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Service\TagService;
use Doctrine\ORM\QueryBuilder;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class TagServiceTest.
 */
class TagServiceTest extends TestCase
{
    private TagRepository&MockObject $tagRepository;
    private PaginatorInterface&MockObject $paginator;
    private TagService $service;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->tagRepository = $this->createMock(TagRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->service = new TagService($this->tagRepository, $this->paginator);
    }

    /**
     * Test getPaginatedList().
     */
    public function testGetPaginatedList(): void
    {
        $page = 2;
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $pagination = $this->createMock(PaginationInterface::class);

        $this->tagRepository
            ->expects(self::once())
            ->method('queryAll')
            ->willReturn($queryBuilder);

        $this->paginator
            ->expects(self::once())
            ->method('paginate')
            ->with($queryBuilder, $page, TagRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($pagination);

        $result = $this->service->getPaginatedList($page);

        self::assertSame($pagination, $result);
    }

    /**
     * Test save().
     */
    public function testSave(): void
    {
        $tag = new Tag();

        $this->tagRepository
            ->expects(self::once())
            ->method('save')
            ->with($tag);

        $this->service->save($tag);
    }

    /**
     * Test delete().
     */
    public function testDelete(): void
    {
        $tag = new Tag();

        $this->tagRepository
            ->expects(self::once())
            ->method('delete')
            ->with($tag);

        $this->service->delete($tag);
    }

    /**
     * Test findOneByTitle().
     */
    public function testFindOneByTitle(): void
    {
        $title = 'Test Tag';
        $tag = new Tag();

        $this->tagRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['title' => $title])
            ->willReturn($tag);

        $result = $this->service->findOneByTitle($title);

        self::assertSame($tag, $result);
    }

    /**
     * Test findOneById().
     */
    public function testFindOneById(): void
    {
        $id = 123;
        $tag = new Tag();

        $this->tagRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => $id])
            ->willReturn($tag);

        $result = $this->service->findOneById($id);

        self::assertSame($tag, $result);
    }
}
