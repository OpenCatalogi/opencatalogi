<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ListingMapperTest extends TestCase
{
    /** @var MockObject|IDBConnection */
    private $db;

    /** @var ListingMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new ListingMapper($this->db);
    }

    // public function testFind()
    // {
    //     $id = 1;

    //     $queryBuilder = $this->createMock(IQueryBuilder::class);
    //     $expressionBuilder = $this->createMock(IQueryBuilder::class);

    //     $queryBuilder->expects($this->once())
    //                  ->method('select')
    //                  ->with('*')
    //                  ->willReturnSelf();

    //     $queryBuilder->expects($this->once())
    //                  ->method('from')
    //                  ->with('listings')
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

    //     $mapperMock = $this->getMockBuilder(ListingMapper::class)
    //                        ->setConstructorArgs([$this->db])
    //                        ->onlyMethods(['findEntity'])
    //                        ->getMock();

    //     $listing = new Listing();
    //     $mapperMock->expects($this->once())
    //                ->method('findEntity')
    //                ->with($queryBuilder)
    //                ->willReturn($listing);

    //     $result = $mapperMock->find($id);
    //     $this->assertInstanceOf(Listing::class, $result);
    // }

    public function testFindAll()
    {
        $limit = 10;
        $offset = 0;

        $queryBuilder = $this->createMock(IQueryBuilder::class);
        $queryBuilder->method('select')->willReturnSelf();
        $queryBuilder->method('from')->willReturnSelf();
        $queryBuilder->method('setMaxResults')->willReturnSelf();
        $queryBuilder->method('setFirstResult')->willReturnSelf();

        $this->db->method('getQueryBuilder')->willReturn($queryBuilder);

        $mapperMock = $this->getMockBuilder(ListingMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['findEntities'])
                           ->getMock();

        $listings = [new Listing(), new Listing()];
        $mapperMock->expects($this->once())
                   ->method('findEntities')
                   ->with($queryBuilder)
                   ->willReturn($listings);

        $result = $mapperMock->findAll($limit, $offset);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testCreateFromArray()
    {
        $object = [
            'title' => 'Test Listing',
            'summary' => 'Test Summary',
            'description' => 'Test Description'
        ];

        $listing = new Listing();
        $listing->hydrate($object);

        $mapperMock = $this->getMockBuilder(ListingMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['insert'])
                           ->getMock();

        $mapperMock->expects($this->once())
                   ->method('insert')
                   ->with($this->isInstanceOf(Listing::class))
                   ->willReturn($listing);

        $result = $mapperMock->createFromArray($object);
        $this->assertInstanceOf(Listing::class, $result);
        $this->assertEquals('Test Listing', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $id = 1;
        $object = [
            'title' => 'Updated Listing',
            'summary' => 'Updated Summary',
            'description' => 'Updated Description'
        ];

        $listing = new Listing();
        $listing->hydrate($object);

        $mapperMock = $this->getMockBuilder(ListingMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['find', 'update'])
                           ->getMock();

        $mapperMock->expects($this->once())
                   ->method('find')
                   ->with($id)
                   ->willReturn($listing);

        $mapperMock->expects($this->once())
                   ->method('update')
                   ->with($this->isInstanceOf(Listing::class))
                   ->willReturn($listing);

        $result = $mapperMock->updateFromArray($id, $object);
        $this->assertInstanceOf(Listing::class, $result);
        $this->assertEquals('Updated Listing', $result->getTitle());
    }
}

?>
