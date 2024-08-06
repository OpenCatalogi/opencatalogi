<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Catalog;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CatalogMapperTest extends TestCase
{
    /** @var MockObject|IDBConnection */
    private $db;

    /** @var CatalogMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new CatalogMapper($this->db);
    }

    
    public function testCreateFromArray()
    {
        $object = [
            'title' => 'Test Catalog',
            'summary' => 'Test Summary',
            'description' => 'Test Description'
        ];

        $catalog = new Catalog();
        $catalog->hydrate($object);

        $mapperMock = $this->getMockBuilder(CatalogMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['insert'])
            ->getMock();

        $mapperMock->method('insert')
            ->willReturn($catalog);

        $result = $mapperMock->createFromArray($object);
        $this->assertInstanceOf(Catalog::class, $result);
        $this->assertEquals('Test Catalog', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $object = [
            'title' => 'Updated Catalog',
            'summary' => 'Updated Summary',
            'description' => 'Updated Description'
        ];

        $catalog = new Catalog();
        $catalog->hydrate($object);

        $mapperMock = $this->getMockBuilder(CatalogMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['find', 'update'])
            ->getMock();

        $mapperMock->method('find')
            ->willReturn($catalog);

        $mapperMock->method('update')
            ->willReturn($catalog);

        $result = $mapperMock->updateFromArray(1, $object);
        $this->assertInstanceOf(Catalog::class, $result);
        $this->assertEquals('Updated Catalog', $result->getTitle());
    }
}
