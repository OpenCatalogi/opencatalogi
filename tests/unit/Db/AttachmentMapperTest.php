<?php

use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCP\IDBConnection;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\DB\QueryBuilder\IExpressionBuilder;
use OCA\OpenCatalogi\Db\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentMapperTest extends TestCase
{
    private $db;
    private $queryBuilder;
    private $expressionBuilder;
    private $attachmentMapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->queryBuilder = $this->createMock(IQueryBuilder::class);
        $this->expressionBuilder = $this->createMock(IExpressionBuilder::class);
        $this->db->method('getQueryBuilder')
                 ->willReturn($this->queryBuilder);

        $this->attachmentMapper = new AttachmentMapper($this->db);
    }

    public function testFind()
{
    $id = 2;

    // Mock the select method to return the query builder instance
    $this->queryBuilder->expects($this->once())
                       ->method('select')
                       ->with('*')
                       ->willReturnSelf();

    // Mock the from method to return the query builder instance
    $this->queryBuilder->expects($this->once())
                       ->method('from')
                       ->with('attachments')
                       ->willReturnSelf();

    // Mock the expr method to return the expression builder instance
    $this->queryBuilder->expects($this->once())
                       ->method('expr')
                       ->willReturn($this->expressionBuilder);

    // Mock the eq method to return a valid expression string
    $this->expressionBuilder->expects($this->once())
                            ->method('eq')
                            ->with('id', $id)
                            ->willReturn('id = 2');

    // Mock the where method to return the query builder instance
    $this->queryBuilder->expects($this->once())
                       ->method('where')
                       ->with('id = 2')
                       ->willReturnSelf();

    // Mock the createNamedParameter method to return the id
    $this->queryBuilder->expects($this->once())
                       ->method('createNamedParameter')
                       ->with($id, IQueryBuilder::PARAM_INT)
                       ->willReturn($id);

    // Mock the findEntity method to return an attachment instance
    $this->attachmentMapper = $this->getMockBuilder(AttachmentMapper::class)
                                   ->setConstructorArgs([$this->db])
                                   ->onlyMethods(['findEntity'])
                                   ->getMock();

    $attachment = new Attachment();
    $this->attachmentMapper->expects($this->once())
                           ->method('findEntity')
                           ->with($this->queryBuilder)
                           ->willReturn($attachment);

    // Call the find method and assert the result
    $result = $this->attachmentMapper->find($id);
    $this->assertInstanceOf(Attachment::class, $result);
}


    public function testFindAll()
    {
        $limit = 10;
        $offset = 0;

        $this->queryBuilder->expects($this->once())
                           ->method('select')
                           ->with('*')
                           ->willReturnSelf();

        $this->queryBuilder->expects($this->once())
                           ->method('from')
                           ->with('attachments')
                           ->willReturnSelf();

        $this->queryBuilder->expects($this->once())
                           ->method('setMaxResults')
                           ->with($limit)
                           ->willReturnSelf();

        $this->queryBuilder->expects($this->once())
                           ->method('setFirstResult')
                           ->with($offset)
                           ->willReturnSelf();

        $this->attachmentMapper = $this->getMockBuilder(AttachmentMapper::class)
                                       ->setConstructorArgs([$this->db])
                                       ->onlyMethods(['findEntities'])
                                       ->getMock();

        $attachments = [new Attachment(), new Attachment()];
        $this->attachmentMapper->expects($this->once())
                               ->method('findEntities')
                               ->with($this->queryBuilder)
                               ->willReturn($attachments);

        $result = $this->attachmentMapper->findAll($limit, $offset);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testCreateFromArray()
    {
        $object = ['title' => 'Test Attachment'];
        $attachment = new Attachment();
        $attachment->hydrate($object);

        $this->attachmentMapper = $this->getMockBuilder(AttachmentMapper::class)
                                       ->setConstructorArgs([$this->db])
                                       ->onlyMethods(['insert'])
                                       ->getMock();

        $this->attachmentMapper->expects($this->once())
                               ->method('insert')
                               ->with($attachment)
                               ->willReturn($attachment);

        $result = $this->attachmentMapper->createFromArray($object);
        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals('Test Attachment', $result->getTitle());
    }

    public function testUpdateFromArray()
    {
        $id = 1;
        $object = ['title' => 'Updated Title'];
        $attachment = new Attachment();
        $attachment->setId($id);
        $attachment->hydrate($object);

        $this->attachmentMapper = $this->getMockBuilder(AttachmentMapper::class)
                                       ->setConstructorArgs([$this->db])
                                       ->onlyMethods(['find', 'update'])
                                       ->getMock();

        $this->attachmentMapper->expects($this->once())
                               ->method('find')
                               ->with($id)
                               ->willReturn($attachment);

        $this->attachmentMapper->expects($this->once())
                               ->method('update')
                               ->with($attachment)
                               ->willReturn($attachment);

        $result = $this->attachmentMapper->updateFromArray($id, $object);
        $this->assertInstanceOf(Attachment::class, $result);
        $this->assertEquals('Updated Title', $result->getTitle());
    }
}

?>
