<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Organisation;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class OrganisationMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'organisations');
	}

	public function find(int $id): Organisation
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('organisations')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('organisations')
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

	public function createFromArray(array $object): Organisation
	{
		$organisation = new Organisation();
		$organisation->hydrate(object: $object);

//		var_dump($organisation->getTitle());

		return $this->insert(entity: $organisation);
	}

	public function updateFromArray(int $id, array $object): Organisation
	{
		$organisation = $this->find($id);
		$organisation->hydrate($object);

		return $this->update($organisation);
	}
}
