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

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('listings')
			->setMaxResults($limit)
			->setFirstResult($offset);

        foreach($filters as $filter => $value) {
            $qb->andWhere($qb->expr()->eq($filter, $qb->createNamedParameter($value)));
        }

        if (!empty($searchConditions)) {
            $qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
            foreach ($searchParams as $param => $value) {
                $qb->setParameter($param, $value);
            }
        }

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Listing
	{
		$listing = new Listing();
		$listing->hydrate(object: $object);

//		var_dump($listing->getTitle());

		return $this->insert(entity: $listing);
	}

	public function updateFromArray(int $id, array $object): Listing
	{
		$listing = $this->find($id);
		$listing->hydrate($object);

		return $this->update($listing);
	}
}
