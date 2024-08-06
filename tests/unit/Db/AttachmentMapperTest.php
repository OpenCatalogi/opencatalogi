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
