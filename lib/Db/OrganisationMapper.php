<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class OrganisationMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'organizations');
	}

	public function find(int $id): Organization
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('organizations')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll($limit = null, $offset = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('organizations')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Organization
	{
		$organization = new Organization();
		$organization->hydrate(object: $object);

//		var_dump($organization->getTitle());

		return $this->insert(entity: $organization);
	}

	public function updateFromArray(int $id, array $object): Organization
	{
		$organization = $this->find($id);
		$organization->hydrate($object);

		return $this->update($organization);
	}
}
