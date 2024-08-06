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

    // public function testFind()
    // {
    //     $id = 1;

    //     $queryBuilder = $this->createMock(IQueryBuilder::class);
    //     $expressionBuilder = $this->createMock(\stdClass::class);

    //     $queryBuilder->expects($this->once())
    //                  ->method('select')
    //                  ->with('*')
    //                  ->willReturnSelf();

    //     $queryBuilder->expects($this->once())
    //                  ->method('from')
    //                  ->with('publications')
    //                  ->willReturnSelf();

    //     $queryBuilder->expects($this->once())
    //                  ->method('expr')
    //                  ->willReturn($expressionBuilder);

    //     $expressionBuilder->expects($this->once())
    //                       ->method('eq')
    //                       ->with('id', $id)
    //                       ->willReturn('id = 1');

    //     $queryBuilder->expects($this->once())
    //                  ->method('where')
    //                  ->with('id = 1')
    //                  ->willReturnSelf();

    //     $queryBuilder->expects($this->once())
    //                  ->method('createNamedParameter')
    //                  ->with($id, IQueryBuilder::PARAM_INT)
    //                  ->willReturn($id);

    //     $this->db->method('getQueryBuilder')
    //              ->willReturn($queryBuilder);

    //     $mapperMock = $this->getMockBuilder(PublicationMapper::class)
    //                        ->setConstructorArgs([$this->db])
    //                        ->onlyMethods(['findEntity'])
    //                        ->getMock();

    //     $publication = new Publication();
    //     $mapperMock->expects($this->once())
    //                ->method('findEntity')
    //                ->with($queryBuilder)
    //                ->willReturn($publication);

    //     $result = $mapperMock->find($id);
    //     $this->assertInstanceOf(Publication::class, $result);
    // }

    // public function testFindAll()
    // {
    //     $limit = 10;
    //     $offset = 0;
    //     $filters = ['title' => 'Test Title'];

    //     $queryBuilder = $this->createMock(IQueryBuilder::class);
    //     $expressionBuilder = $this->createMock(\stdClass::class);

    //     $queryBuilder->method('select')->willReturnSelf();
    //     $queryBuilder->method('from')->willReturnSelf();
    //     $queryBuilder->method('setMaxResults')->willReturnSelf();
    //     $queryBuilder->method('setFirstResult')->willReturnSelf();
    //     $queryBuilder->method('expr')->willReturn($expressionBuilder);
    //     $queryBuilder->method('andWhere')->willReturnSelf();
    //     $queryBuilder->method('createNamedParameter')->willReturnArgument(0);

    //     $expressionBuilder->method('eq')->willReturn('title = "Test Title"');

    //     $this->db->method('getQueryBuilder')->willReturn($queryBuilder);

    //     $mapperMock = $this->getMockBuilder(PublicationMapper::class)
    //                        ->setConstructorArgs([$this->db])
    //                        ->onlyMethods(['findEntities'])
    //                        ->getMock();

    //     $publications = [new Publication(), new Publication()];
    //     $mapperMock->expects($this->once())
    //                ->method('findEntities')
    //                ->with($queryBuilder)
    //                ->willReturn($publications);

    //     $result = $mapperMock->findAll($limit, $offset, $filters);
    //     $this->assertIsArray($result);
    //     $this->assertCount(2, $result);
    // }

    public function testCreateFromArray()
    {
        $publicationData = [
            'title' => 'New Publication',
            'summary' => 'Summary of new publication'
        ];

        $publication = new Publication();
        $publication->hydrate($publicationData);

        $mapperMock = $this->getMockBuilder(PublicationMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['insert'])
                           ->getMock();

        $mapperMock->expects($this->once())
                   ->method('insert')
                   ->with($this->isInstanceOf(Publication::class))
                   ->willReturn($publication);

        $result = $mapperMock->createFromArray($publicationData);
        $this->assertInstanceOf(Publication::class, $result);
        $this->assertEquals('New Publication', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $id = 1;
        $publicationData = [
            'title' => 'Updated Publication',
            'summary' => 'Updated summary of publication'
        ];

        $publication = new Publication();
        $publication->hydrate(['id' => $id]);

        $mapperMock = $this->getMockBuilder(PublicationMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['find', 'update'])
                           ->getMock();

        $mapperMock->expects($this->once())
                   ->method('find')
                   ->with($id)
                   ->willReturn($publication);

        $mapperMock->expects($this->once())
                   ->method('update')
                   ->with($this->isInstanceOf(Publication::class))
                   ->willReturn($publication);

        $result = $mapperMock->updateFromArray($id, $publicationData);
        $this->assertInstanceOf(Publication::class, $result);
        $this->assertEquals('Updated Publication', $result->getTitle());
    }
}

?>
