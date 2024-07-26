<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Listing;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ListingMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'listings');
	}

	public function find(int $id): Listing
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('listings')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll($limit = null, $offset = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('listings')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Listing
	{
		$publication = new Listing();
		$publication->hydrate(object: $object);

//		var_dump($publication->getTitle());

		return $this->insert(entity: $publication);
	}

	public function updateFromArray(int $id, array $object): Listing
	{
		$publication = $this->find($id);
		$publication->hydrate($object);

		return $this->update($publication);
	}
}
