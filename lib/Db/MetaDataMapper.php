<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class MetaDataMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'metadata');
	}

	public function find(int $id): MetaData
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('metadata')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll($limit = null, $offset = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('metadata')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): MetaData
	{
		$publication = new MetaData();
		$publication->hydrate(object: $object);

//		var_dump($publication->getTitle());

		return $this->insert(entity: $publication);
	}

	public function updateFromArray(int $id, array $object): MetaData
	{
		$publication = $this->find($id);
		$publication->hydrate($object);

		return $this->update($publication);
	}
}
