<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class CatalogMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'catalogi');
	}

	public function find(int $id): Catalog
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('catalogi')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll($limit = null, $offset = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('catalogi')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return ['results' => $this->findEntities(query: $qb)];
	}

	public function createFromArray(array $object): Catalog
	{
		$publication = new Catalog();
		$publication->hydrate(object: $object);

//		var_dump($publication->getTitle());

		return $this->insert(entity: $publication);
	}

	public function updateFromArray(int $id, array $object): Catalog
	{
		$publication = $this->find($id);
		$publication->hydrate($object);

		return $this->update($publication);
	}
}
