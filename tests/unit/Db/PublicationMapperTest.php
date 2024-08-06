<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PublicationMapperTest extends TestCase
{
    /** @var MockObject|IDBConnection */
    private $db;

    /** @var PublicationMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new PublicationMapper($this->db);
    }

    public function testFind()
    {
        $qb = $this->createMock(IQueryBuilder::class);
        $qb->method('select')->willReturn($qb);
        $qb->method('from')->willReturn($qb);
        $qb->method('where')->willReturn($qb);
        $qb->method('setMaxResults')->willReturn($qb);
        $qb->method('setFirstResult')->willReturn($qb);
        $qb->method('createNamedParameter')->willReturnArgument(0);

        $this->db->method('getQueryBuilder')->willReturn($qb);

        $publication = new Publication();
        $publication->setId(1);

        $this->mapper->method('findEntity')->willReturn($publication);

        $result = $this->mapper->find(1);
        $this->assertInstanceOf(Publication::class, $result);
        $this->assertEquals(1, $result->getId());
    }

    public function testFindAll()
    {
        $qb = $this->createMock(IQueryBuilder::class);
        $qb->method('select')->willReturn($qb);
        $qb->method('from')->willReturn($qb);
        $qb->method('setMaxResults')->willReturn($qb);
        $qb->method('setFirstResult')->willReturn($qb);
        $qb->method('andWhere')->willReturn($qb);
        $qb->method('createNamedParameter')->willReturnArgument(0);

        $this->db->method('getQueryBuilder')->willReturn($qb);

        $publication = new Publication();
        $publication->setId(1);

        $this->mapper->method('findEntities')->willReturn([$publication]);

        $result = $this->mapper->findAll(10, 0, ['title' => 'Test']);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Publication::class, $result[0]);
    }

    public function testCreateFromArray()
    {
        $publicationData = [
            'title' => 'New Publication',
            'summary' => 'Summary of new publication'
        ];

        $publication = new Publication();
        $publication->hydrate($publicationData);

        $this->mapper->method('insert')->willReturn($publication);

        $result = $this->mapper->createFromArray($publicationData);
        $this->assertInstanceOf(Publication::class, $result);
        $this->assertEquals('New Publication', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $publicationData = [
            'title' => 'Updated Publication',
            'summary' => 'Updated summary of publication'
        ];

        $publication = new Publication();
        $publication->hydrate(['id' => 1]);
        $this->mapper->method('find')->willReturn($publication);
        $this->mapper->method('update')->willReturn($publication);

        $result = $this->mapper->updateFromArray(1, $publicationData);
        $this->assertInstanceOf(Publication::class, $result);
        $this->assertEquals('Updated Publication', $result->getTitle());
    }
}
