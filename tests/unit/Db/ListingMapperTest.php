<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

    public function testFind()
    {
        $qb = $this->createMock(IQueryBuilder::class);
        $expr = $this->createMock(IQueryBuilder::class);

        $qb->method('select')->willReturnSelf();
        $qb->method('from')->willReturnSelf();
        $qb->method('where')->willReturnSelf();
        $qb->method('expr')->willReturn($expr);
        $expr->method('eq')->willReturnSelf();
        $qb->method('createNamedParameter')->willReturnSelf();
        $qb->method('execute')->willReturnSelf();
        $qb->method('fetch')->willReturn([
            'id' => 1,
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'search' => 'search query',
            'directory' => 'directory',
            'metadata' => 'metadata',
            'status' => 'status',
            'lastSync' => '2023-01-01T00:00:00+00:00',
            'default' => 1,
            'available' => 1,
        ]);

        $this->db->method('getQueryBuilder')->willReturn($qb);

        $listing = $this->mapper->find(1);

        $this->assertInstanceOf(Listing::class, $listing);
        $this->assertEquals(1, $listing->getId());
        $this->assertEquals('Test Title', $listing->getTitle());
        $this->assertEquals('Test Summary', $listing->getSummary());
        $this->assertEquals('Test Description', $listing->getDescription());
        $this->assertEquals('search query', $listing->getSearch());
        $this->assertEquals('directory', $listing->getDirectory());
        $this->assertEquals('metadata', $listing->getMetadata());
        $this->assertEquals('status', $listing->getStatus());
        $this->assertEquals('2023-01-01T00:00:00+00:00', $listing->getLastSync()->format('c'));
        $this->assertTrue($listing->getDefault());
        $this->assertTrue($listing->getAvailable());
    }

    public function testFindAll()
    {
        $qb = $this->createMock(IQueryBuilder::class);
        $expr = $this->createMock(IQueryBuilder::class);

        $qb->method('select')->willReturnSelf();
        $qb->method('from')->willReturnSelf();
        $qb->method('setMaxResults')->willReturnSelf();
        $qb->method('setFirstResult')->willReturnSelf();
        $qb->method('execute')->willReturnSelf();
        $qb->method('fetchAll')->willReturn([
            [
                'id' => 1,
                'title' => 'Test Title',
                'summary' => 'Test Summary',
                'description' => 'Test Description',
                'search' => 'search query',
                'directory' => 'directory',
                'metadata' => 'metadata',
                'status' => 'status',
                'lastSync' => '2023-01-01T00:00:00+00:00',
                'default' => 1,
                'available' => 1,
            ]
        ]);

        $this->db->method('getQueryBuilder')->willReturn($qb);

        $listings = $this->mapper->findAll();

        $this->assertIsArray($listings);
        $this->assertCount(1, $listings);

        $listing = $listings[0];
        $this->assertInstanceOf(Listing::class, $listing);
        $this->assertEquals(1, $listing->getId());
        $this->assertEquals('Test Title', $listing->getTitle());
        $this->assertEquals('Test Summary', $listing->getSummary());
        $this->assertEquals('Test Description', $listing->getDescription());
        $this->assertEquals('search query', $listing->getSearch());
        $this->assertEquals('directory', $listing->getDirectory());
        $this->assertEquals('metadata', $listing->getMetadata());
        $this->assertEquals('status', $listing->getStatus());
        $this->assertEquals('2023-01-01T00:00:00+00:00', $listing->getLastSync()->format('c'));
        $this->assertTrue($listing->getDefault());
        $this->assertTrue($listing->getAvailable());
    }

    public function testCreateFromArray()
    {
        $object = [
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'search' => 'search query',
            'directory' => 'directory',
            'metadata' => 'metadata',
            'status' => 'status',
            'lastSync' => '2023-01-01T00:00:00+00:00',
            'default' => true,
            'available' => true,
        ];

        $listing = new Listing();
        $listing->hydrate($object);

        $qb = $this->createMock(IQueryBuilder::class);
        $this->db->method('getQueryBuilder')->willReturn($qb);

        $mapper = $this->getMockBuilder(ListingMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['insert'])
            ->getMock();

        $mapper->method('insert')->willReturn($listing);

        $createdListing = $mapper->createFromArray($object);

        $this->assertInstanceOf(Listing::class, $createdListing);
        $this->assertEquals('Test Title', $createdListing->getTitle());
        $this->assertEquals('Test Summary', $createdListing->getSummary());
        $this->assertEquals('Test Description', $createdListing->getDescription());
        $this->assertEquals('search query', $createdListing->getSearch());
        $this->assertEquals('directory', $createdListing->getDirectory());
        $this->assertEquals('metadata', $createdListing->getMetadata());
        $this->assertEquals('status', $createdListing->getStatus());
        $this->assertEquals('2023-01-01T00:00:00+00:00', $createdListing->getLastSync()->format('c'));
        $this->assertTrue($createdListing->getDefault());
        $this->assertTrue($createdListing->getAvailable());
    }

    public function testUpdateFromArray()
    {
        $object = [
            'title' => 'Updated Title',
            'summary' => 'Updated Summary',
            'description' => 'Updated Description',
            'search' => 'updated search query',
            'directory' => 'updated directory',
            'metadata' => 'updated metadata',
            'status' => 'updated status',
            'lastSync' => '2023-01-01T00:00:00+00:00',
            'default' => false,
            'available' => false,
        ];

        $listing = $this->createMock(Listing::class);
        $listing->method('hydrate')->willReturnSelf();

        $mapper = $this->getMockBuilder(ListingMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['find', 'update'])
            ->getMock();

        $mapper->method('find')->willReturn($listing);
        $mapper->method('update')->willReturn($listing);

        $updatedListing = $mapper->updateFromArray(1, $object);

        $this->assertInstanceOf(Listing::class, $updatedListing);
        $this->assertEquals('Updated Title', $updatedListing->getTitle());
        $this->assertEquals('Updated Summary', $updatedListing->getSummary());
        $this->assertEquals('Updated Description', $updatedListing->getDescription());
        $this->assertEquals('updated search query', $updatedListing->getSearch());
        $this->assertEquals('updated directory', $updatedListing->getDirectory());
        $this->assertEquals('updated metadata', $updatedListing->getMetadata());
        $this->assertEquals('updated status', $updatedListing->getStatus());
        $this->assertEquals('2023-01-01T00:00:00+00:00', $updatedListing->getLastSync()->format('c'));
        $this->assertFalse($updatedListing->getDefault());
        $this->assertFalse($updatedListing->getAvailable());
    }
}
