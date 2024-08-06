<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Organization;
use OCA\OpenCatalogi\Db\OrganizationMapper;
use OCP\IDBConnection;
use OCP\DB\QueryBuilder\IQueryBuilder;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class OrganizationMapperTest extends TestCase
{
    /** @var MockObject|IDBConnection */
    private $db;

    /** @var OrganizationMapper */
    private $organizationMapper;

    protected function setUp(): void
    {
        $this->db = $this->createMock(IDBConnection::class);
        $this->organizationMapper = new OrganizationMapper($this->db);
    }

    public function testFind()
    {
        $id = 1;
        $organization = new Organization();
        $organization->setId($id);

        $qb = $this->createMock(IQueryBuilder::class);
        $qb->expects($this->once())
            ->method('select')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('from')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('setParameters')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('execute')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('fetch')
            ->willReturn(['id' => $id]);

        $this->db->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($qb);

        $result = $this->organizationMapper->find($id);

        $this->assertInstanceOf(Organization::class, $result);
        $this->assertEquals($id, $result->getId());
    }

    public function testFindAll()
    {
        $organizations = [
            ['id' => 1, 'title' => 'Org 1'],
            ['id' => 2, 'title' => 'Org 2']
        ];

        $qb = $this->createMock(IQueryBuilder::class);
        $qb->expects($this->once())
            ->method('select')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('from')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('setMaxResults')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('setFirstResult')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('execute')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('fetchAll')
            ->willReturn($organizations);

        $this->db->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($qb);

        $result = $this->organizationMapper->findAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testCreateFromArray()
    {
        $data = [
            'title' => 'Test Org',
            'summary' => 'Summary of test org'
        ];

        $organization = new Organization();
        $organization->hydrate($data);

        $qb = $this->createMock(IQueryBuilder::class);
        $qb->expects($this->once())
            ->method('insert')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('values')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('execute');

        $this->db->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($qb);

        $result = $this->organizationMapper->createFromArray($data);

        $this->assertInstanceOf(Organization::class, $result);
    }

    public function testUpdateFromArray()
    {
        $id = 1;
        $data = [
            'title' => 'Updated Org',
            'summary' => 'Updated summary'
        ];

        $organization = new Organization();
        $organization->setId($id);
        $organization->hydrate($data);

        $qb = $this->createMock(IQueryBuilder::class);
        $qb->expects($this->once())
            ->method('update')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('set')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('where')
            ->willReturnSelf();
        $qb->expects($this->once())
            ->method('execute');

        $this->db->expects($this->once())
            ->method('getQueryBuilder')
            ->willReturn($qb);

        $result = $this->organizationMapper->updateFromArray($id, $data);

        $this->assertInstanceOf(Organization::class, $result);
        $this->assertEquals('Updated Org', $result->getTitle());
    }
}
