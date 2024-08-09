<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class PublicationMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'publications');
	}

	public function find(int $id): Publication
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('publications')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function count(?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): int
	{
		$qb = $this->db->getQueryBuilder();

		$qb->selectAlias($qb->createFunction('COUNT(*)'), 'count')
			->from('publications');

		foreach($filters as $filter => $value) {
			$qb->andWhere($qb->expr()->eq($filter, $qb->createNamedParameter($value)));
		}

		if (!empty($searchConditions)) {
			$qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
			foreach ($searchParams as $param => $value) {
				$qb->setParameter($param, $value);
			}
		}

		$cursor = $qb->execute();
		$row = $cursor->fetch();
		$cursor->closeCursor();

		return $row['count'];
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('publications')
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

	public function createFromArray(array $object): Publication
	{
		$publication = new Publication();
		$publication->hydrate(object: $object);

//		var_dump($publication->getTitle());

		return $this->insert(entity: $publication);
	}

	public function updateFromArray(int $id, array $object): Publication
	{
		$publication = $this->find(id: $id);
		$publication->hydrate(object: $object);

		return $this->update($publication);
	}
}
