<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Attachment;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AttachmentMapperTest extends TestCase
{
    /** @var MockObject|IDBConnection */
    private $db;

    /** @var AttachmentMapper */
    private $mapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->mapper = new AttachmentMapper($this->db);
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

        $attachment = new Attachment();
        $attachment->setId(1);
        $attachment->setTitle('Test Attachment');

        $mapperMock = $this->getMockBuilder(AttachmentMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['findEntity'])
            ->getMock();

        $mapperMock->method('findEntity')
            ->willReturn($attachment);

        $result = $mapperMock->find(1);
        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals('Test Attachment', $result->getTitle());
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

        $attachment = new Attachment();
        $attachment->setId(1);
        $attachment->setTitle('Test Attachment');

        $mapperMock = $this->getMockBuilder(AttachmentMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['findEntities'])
            ->getMock();

        $mapperMock->method('findEntities')
            ->willReturn([$attachment]);

        $result = $mapperMock->findAll();
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Attachment::class, $result[0]);
        $this->assertEquals('Test Attachment', $result[0]->getTitle());
    }

    public function testCreateFromArray()
    {
        $object = [
            'title' => 'Test Attachment',
            'summary' => 'Test Summary',
            'description' => 'Test Description'
        ];

        $attachment = new Attachment();
        $attachment->hydrate($object);

        $mapperMock = $this->getMockBuilder(AttachmentMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['insert'])
            ->getMock();

        $mapperMock->method('insert')
            ->willReturn($attachment);

        $result = $mapperMock->createFromArray($object);
        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals('Test Attachment', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $object = [
            'title' => 'Updated Attachment',
            'summary' => 'Updated Summary',
            'description' => 'Updated Description'
        ];

        $attachment = new Attachment();
        $attachment->hydrate($object);

        $mapperMock = $this->getMockBuilder(AttachmentMapper::class)
            ->setConstructorArgs([$this->db])
            ->onlyMethods(['find', 'update'])
            ->getMock();

        $mapperMock->method('find')
            ->willReturn($attachment);

        $mapperMock->method('update')
            ->willReturn($attachment);

        $result = $mapperMock->updateFromArray(1, $object);
        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals('Updated Attachment', $result->getTitle());
    }
}
