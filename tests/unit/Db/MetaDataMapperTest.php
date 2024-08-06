<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\MetaData;
use OCA\OpenCatalogi\Db\MetaDataMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MetaDataMapperTest extends TestCase
{
    /** @var MockObject|IDBConnection */
    private $db;

    /** @var MetaDataMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new MetaDataMapper($this->db);
    }

    // Trying to configure method "eq" which cannot be configured because it does not exist, has not been specified, is final, or is static
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
    //                  ->with('metadata')
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

    //     $mapperMock = $this->getMockBuilder(MetaDataMapper::class)
    //                        ->setConstructorArgs([$this->db])
    //                        ->onlyMethods(['findEntity'])
    //                        ->getMock();

    //     $metadata = new MetaData();
    //     $mapperMock->expects($this->once())
    //                ->method('findEntity')
    //                ->with($queryBuilder)
    //                ->willReturn($metadata);

    //     $result = $mapperMock->find($id);
    //     $this->assertInstanceOf(MetaData::class, $result);
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

        $mapperMock = $this->getMockBuilder(MetaDataMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['findEntities'])
                           ->getMock();

        $metadatas = [new MetaData(), new MetaData()];
        $mapperMock->expects($this->once())
                   ->method('findEntities')
                   ->with($queryBuilder)
                   ->willReturn($metadatas);

        $result = $mapperMock->findAll($limit, $offset);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testCreateFromArray()
    {
        $object = [
            'title' => 'Test MetaData',
            'version' => '1.0.0',
            'description' => 'Test Description',
            'required' => ['field1', 'field2'],
            'properties' => ['property1' => 'value1', 'property2' => 'value2']
        ];

        $metadata = new MetaData();
        $metadata->hydrate($object);

        $mapperMock = $this->getMockBuilder(MetaDataMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['insert'])
                           ->getMock();

        $mapperMock->expects($this->once())
                   ->method('insert')
                   ->with($this->isInstanceOf(MetaData::class))
                   ->willReturn($metadata);

        $result = $mapperMock->createFromArray($object);
        $this->assertInstanceOf(MetaData::class, $result);
        $this->assertEquals('Test MetaData', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $id = 1;
        $object = [
            'title' => 'Updated MetaData',
            'version' => '1.0.1',
            'description' => 'Updated Description',
            'required' => ['field1', 'field2'],
            'properties' => ['property1' => 'newValue1', 'property2' => 'newValue2']
        ];

        $metadata = new MetaData();
        $metadata->hydrate($object);

        $mapperMock = $this->getMockBuilder(MetaDataMapper::class)
                           ->setConstructorArgs([$this->db])
                           ->onlyMethods(['find', 'update'])
                           ->getMock();

        $mapperMock->expects($this->once())
                   ->method('find')
                   ->with($id)
                   ->willReturn($metadata);

        $mapperMock->expects($this->once())
                   ->method('update')
                   ->with($this->isInstanceOf(MetaData::class))
                   ->willReturn($metadata);

        $result = $mapperMock->updateFromArray($id, $object);
        $this->assertInstanceOf(MetaData::class, $result);
        $this->assertEquals('Updated MetaData', $result->getTitle());
    }
}

?>
