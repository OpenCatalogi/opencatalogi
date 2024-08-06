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

    public function testFind()
    {
        $qb = $this->createMock(IQueryBuilder::class);

        $this->db->method('getQueryBuilder')
            ->willReturn($qb);

        $qb->method('select')
            ->willReturnSelf();
        $qb->method('from')
            ->willReturnSelf();
        $qb->method('where')
            ->willReturnSelf();
        $qb->method('setParameter')
            ->willReturnSelf();

        $qb->method('execute')
            ->willReturn($this->createMock(IResult::class));

        $catalog = new Catalog();
        $catalog->setId(1);
        $catalog->setTitle('Test Catalog');

        $mapperMock = $this->getMockBuilder(CatalogMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['findEntity'])
            ->getMock();

        $mapperMock->method('findEntity')
            ->willReturn($catalog);

        $result = $mapperMock->find(1);
        $this->assertInstanceOf(Catalog::class, $result);
        $this->assertEquals('Test Catalog', $result->getTitle());
    }

    public function testFindAll()
    {
        $qb = $this->createMock(IQueryBuilder::class);

        $this->db->method('getQueryBuilder')
            ->willReturn($qb);

        $qb->method('select')
            ->willReturnSelf();
        $qb->method('from')
            ->willReturnSelf();
        $qb->method('setMaxResults')
            ->willReturnSelf();
        $qb->method('setFirstResult')
            ->willReturnSelf();

        $qb->method('execute')
            ->willReturn($this->createMock(IResult::class));

        $catalog = new Catalog();
        $catalog->setId(1);
        $catalog->setTitle('Test Catalog');

        $mapperMock = $this->getMockBuilder(CatalogMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['findEntities'])
            ->getMock();

        $mapperMock->method('findEntities')
            ->willReturn([$catalog]);

        $result = $mapperMock->findAll();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Catalog::class, $result[0]);
        $this->assertEquals('Test Catalog', $result[0]->getTitle());
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
