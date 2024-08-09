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

	private function parseComplexFilter(IQueryBuilder $queryBuilder, array $filter, string $name): IQueryBuilder
	{
		foreach($filter as $key => $value) {
			switch($key) {
				case '>=':
				case 'after':
					$queryBuilder->andWhere($queryBuilder->expr()->gte($name, $queryBuilder->createNamedParameter($value)));
					break;
				case '>':
				case 'strictly_after':
					$queryBuilder->andWhere($queryBuilder->expr()->gt($name, $queryBuilder->createNamedParameter($value)));
					break;
				case '<=':
				case 'before':
					$queryBuilder->andWhere($queryBuilder->expr()->lte($name, $queryBuilder->createNamedParameter($value)));
					break;
				case '<':
				case 'strictly_before':
					$queryBuilder->andWhere($queryBuilder->expr()->lt($name, $queryBuilder->createNamedParameter($value)));
					break;
				default:
					$queryBuilder->andWhere($queryBuilder->expr()->eq($name, $queryBuilder->createNamedParameter($filter)));
			}
		}

		return $queryBuilder;
	}

	private function addFilters(IQueryBuilder $queryBuilder, array $filters): IQueryBuilder
	{
		foreach($filters as $key => $filter) {
			if(is_array($filter) === false) {
				$queryBuilder->andWhere($queryBuilder->expr()->eq($filter, $queryBuilder->createNamedParameter($filter)));
				continue;
			}

			$queryBuilder = $this->parseComplexFilter(queryBuilder: $queryBuilder, filter: $filter, name: $key);
		}

		return $queryBuilder;
	}

	public function count(?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): int
	{


		$qb = $this->db->getQueryBuilder();

		$qb->selectAlias($qb->createFunction('COUNT(*)'), 'count')
			->from('publications');


		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);


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

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = [], ?array $sort = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('publications')
			->setMaxResults($limit)
			->setFirstResult($offset);

		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);

        if (empty($searchConditions) === false) {
            $qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
            foreach ($searchParams as $param => $value) {
                $qb->setParameter($param, $value);
            }
        }

		if (empty($sort) === false) {
			foreach ($sort as $field => $direction) {
				$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
				$qb->addOrderBy($field, $direction);
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
