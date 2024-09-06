<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\DB\Types;
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

		$qb->select(
			'p.*',
					'c.id AS catalogi_id',
					'c.title AS catalogi_title',
					'c.summary AS catalogi_summary',
					'c.description AS catalogi_description',
					'c.image AS catalogi_image',
					'c.search AS catalogi_search',
					'c.listed AS catalogi_listed',
					'c.organisation AS catalogi_organisation',
					'c.metadata AS catalogi_metadata',
			)
			->from('publications', 'p')
			->leftJoin('p', 'catalogi', 'c', 'p.catalogi = c.id')
			->where(
				$qb->expr()->eq('p.id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntityCustom(query: $qb);
	}

	/**
	 * Returns a db result and throws exceptions when there are more or less
	 * results CUSTOM FOR JOINS
	 *
	 * @param IQueryBuilder $query
	 * @return Entity the entity
	 * @psalm-return T the entity
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException if more than one item exist
	 * @throws DoesNotExistException if the item does not exist
	 * @since 14.0.0
	 */
	protected function findEntityCustom(IQueryBuilder $query): Entity {
		return $this->mapRowToEntityCustom($this->findOneQuery($query));
	}

	/**
	 *  CUSTOM FOR JOINS
	 */
	protected function mapRowToEntityCustom(array $row): Entity {
		unset($row['DOCTRINE_ROWNUM']); // remove doctrine/dbal helper column

		// Map the Catalogi fields to a sub-array
		$catalogiData = [
			'id' => $row['catalogi_id'] ?? null,
			'title' => $row['catalogi_title'] ?? null,
			'summary' => $row['catalogi_summary'] ?? null,
			'description' => $row['catalogi_description'] ?? null,
			'image' => $row['catalogi_image'] ?? null,
			'search' => $row['catalogi_search'] ?? null,
			'listed' => $row['catalogi_listed'] ?? null,
			'organisation' => $row['catalogi_organisation'] ?? null,
			'metadata' => $row['catalogi_metadata'] ?? null,
		];

		$catalogiIsEmpty = true;
		foreach ($catalogiData as $key => $value) {
			if ($value !== null) {
				$catalogiIsEmpty = false;
			}

			if (array_key_exists("catalogi_$key", $row) === true) {
				unset($row["catalogi_$key"]);
			}
		}

		$row['catalogi'] = $catalogiIsEmpty === true ? null : json_encode(Catalog::fromRow($catalogiData)->jsonSerialize());

		return \call_user_func($this->entityClass .'::fromRow', $row);
	}

	/**
	 * Runs a sql query and returns an array of entities CUSTOM FOR JOINS
	 *
	 * @param IQueryBuilder $query
	 * @return Entity[] all fetched entities
	 * @psalm-return T[] all fetched entities
	 * @throws Exception
	 * @since 14.0.0
	 */
	protected function findEntitiesCustom(IQueryBuilder $query): array {
		$result = $query->executeQuery();
		try {
			$entities = [];
			while ($row = $result->fetch()) {
				$entities[] = $this->mapRowToEntityCustom($row);
			}
			return $entities;
		} finally {
			$result->closeCursor();
		}
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
					$queryBuilder->andWhere($queryBuilder->expr()->eq(x: $name, y: $queryBuilder->createNamedParameter($filter)));
			}
		}

		return $queryBuilder;
	}

	private function addFilters(IQueryBuilder $queryBuilder, array $filters): IQueryBuilder
	{
		foreach($filters as $key => $filter) {
			if(is_array($filter) === false) {
				$queryBuilder->andWhere($queryBuilder->expr()->eq($key, $queryBuilder->createNamedParameter($filter)));
				$queryBuilder->setParameter($key, $filter);
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

		$qb->select(
				'p.*',
				'c.id AS catalogi_id',
				'c.title AS catalogi_title',
				'c.summary AS catalogi_summary',
				'c.description AS catalogi_description',
				'c.image AS catalogi_image',
				'c.search AS catalogi_search',
				'c.listed AS catalogi_listed',
				'c.organisation AS catalogi_organisation',
				'c.metadata AS catalogi_metadata',
			)
			->from('publications', 'p')
			->leftJoin('p', 'catalogi', 'c', 'p.catalogi = c.id')
			->setMaxResults($limit)
			->setFirstResult($offset);

		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);

        // Add search conditions
        if (!empty($searchConditions)) {
            foreach ($searchConditions as $condition) {
                $qb->andWhere($condition);
            }
    
            // Bind all parameters at once using setParameters()
            $paramBindings = [];
            foreach ($searchParams as $param => $value) {
                // Handle catalogi parameters explicitly as integers
                if (strpos($param, ':catalogi_') === 0) {
                    $paramBindings[$param] = [$value, \PDO::PARAM_INT];
                } else {
                    // For all other parameters, bind normally
                    $paramBindings[$param] = $value;
                }
            }
    
            // Use setParameters to bind all at once
            foreach ($paramBindings as $param => $binding) {
                if (is_array($binding) === true) {
                    $qb->setParameter($param, $binding[0], $binding[1]);  // Bind with type
                } else {
                    $qb->setParameter($param, $binding);  // Bind normally
                }
            }
        }

		if (empty($sort) === false) {
			foreach ($sort as $field => $direction) {
				$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
				$qb->addOrderBy($field, $direction);
			}
		}
		// Use the existing findEntities method to fetch and map the results
		return $this->findEntitiesCustom($qb);
	}

	public function createFromArray(array $object): Publication
	{
		$publication = new Publication();
		$publication->hydrate(object: $object);

		$publication = $this->insert(entity: $publication);

		return $this->find($publication->getId());
	}

	public function updateFromArray(int $id, array $object): Publication
	{
		$publication = $this->find(id: $id);
		$publication->hydrate(object: $object);

		$publication = $this->update($publication);

		return $this->find($publication->getId());
	}
}
