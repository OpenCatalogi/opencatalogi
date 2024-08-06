<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Catalog;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCP\IDBConnection;
use OCP\DB\QueryBuilder\IQueryBuilder;
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

        $mapperMock = $this->getMockBuilder(CatalogMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['findEntities'])
                           ->getMock();

        $catalogs = [new Catalog(), new Catalog()];
        $mapperMock->expects($this->once())
                   ->method('findEntities')
                   ->with($queryBuilder)
                   ->willReturn($catalogs);

        $result = $mapperMock->findAll($limit, $offset);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
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

        $mapperMock->expects($this->once())
                   ->method('insert')
                   ->with($this->isInstanceOf(Catalog::class))
                   ->willReturn($catalog);

        $result = $mapperMock->createFromArray($object);
        $this->assertInstanceOf(Catalog::class, $result);
        $this->assertEquals('Test Catalog', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $id = 1;
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

        $mapperMock->expects($this->once())
                   ->method('find')
                   ->with($id)
                   ->willReturn($catalog);

        $mapperMock->expects($this->once())
                   ->method('update')
                   ->with($this->isInstanceOf(Catalog::class))
                   ->willReturn($catalog);

        $result = $mapperMock->updateFromArray($id, $object);
        $this->assertInstanceOf(Catalog::class, $result);
        $this->assertEquals('Updated Catalog', $result->getTitle());
    }
}

?>
