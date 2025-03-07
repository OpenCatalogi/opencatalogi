<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\DB\Types;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Class PublicationMapper
 *
 * This class is responsible for mapping Publication entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Publication entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class PublicationMapper extends QBMapper
{
	/**
	 * Constructor for PublicationMapper
	 *
	 * @param IDBConnection $db The database connection
	 * @param IURLGenerator $urlGenerator The URL generator
	 */
	public function __construct(IDBConnection $db, IURLGenerator $urlGenerator)
	{
		parent::__construct($db, tableName: 'ocat_publications');
	}

	/**
	 * Find a Publication by its ID or UUID
	 *
	 * @param int|string $id The ID or UUID of the Publication
	 * @return Publication The found Publication entity
	 */
	public function find($id): Publication|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publications')
			->where($qb->expr()->orX(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)),
				$qb->expr()->eq('uuid', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR))
			));

		try {
			return $this->findEntity($qb);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return null;
		}
	}

	/**
	 * Find multiple Publications by their IDs or UUIDs
	 *
	 * @param array $ids An array of IDs or UUIDs
	 * @return array An array of found Publication entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publications')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Parse complex filter conditions and add them to the query builder
	 *
	 * @param IQueryBuilder $queryBuilder The query builder instance
	 * @param array $filter The filter conditions
	 * @param string $name The name of the field to filter
	 * @return IQueryBuilder The updated query builder
	 */
	private function parseComplexFilter(IQueryBuilder $queryBuilder, array $filter, string $name): IQueryBuilder
	{
		foreach ($filter as $key => $value) {
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

	/**
	 * Add filters to the query builder
	 *
	 * @param IQueryBuilder $queryBuilder The query builder instance
	 * @param array $filters The filters to add
	 * @return IQueryBuilder The updated query builder
	 */
	private function addFilters(IQueryBuilder $queryBuilder, array $filters): IQueryBuilder
	{
		foreach ($filters as $key => $filter) {
			if (is_array($filter) === false) {
				$queryBuilder->andWhere($queryBuilder->expr()->eq($key, $queryBuilder->createNamedParameter($filter)));
				$queryBuilder->setParameter($key, $filter);
				continue;
			}

			$queryBuilder = $this->parseComplexFilter(queryBuilder: $queryBuilder, filter: $filter, name: $key);
		}

		return $queryBuilder;
	}

	/**
	 * Count the number of Publications matching the given filters and search conditions
	 *
	 * @param array|null $filters The filters to apply
	 * @param array|null $searchConditions The search conditions to apply
	 * @param array|null $searchParams The search parameters
	 * @return int The count of matching Publications
	 */
	public function count(?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): int
	{
		$qb = $this->db->getQueryBuilder();

		$qb->selectAlias($qb->createFunction('COUNT(*)'), 'count')
			->from('ocat_publications');

		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);

		if (empty($searchConditions) === false) {
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

	/**
	 * Find all Publications with optional filtering, searching, and sorting
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @param array|null $filters Associative array of filters
	 * @param array|null $searchConditions Array of search conditions
	 * @param array|null $searchParams Array of search parameters
	 * @param array|null $sort Associative array of sort fields and directions
	 * @return array An array of found Publication entities
	 */
	public function findAll(?int $limit = null, ?int $offset = null, array $filters = [], array $sort = [], ?string $search = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*',)
			->from('ocat_publications')
			->setMaxResults($limit)
			->setFirstResult($offset);

		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);

		// Add sorting
		if (empty($sort) === false) {
			foreach ($sort as $field => $direction) {
				$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
				$qb->addOrderBy($field, $direction);
			}
		}
		// Use the existing findEntities method to fetch and map the results
		return $this->findEntities($qb);
	}

	/**
	 * Create a new Publication from an array of data
	 *
	 * @param array $object The array of data to create the Publication from
	 * @return Publication The created Publication entity
	 */
	public function createFromArray(array $object): Publication
	{
		$publication = new Publication();
		$publication->hydrate(object: $object);

		// Set uuid if not provided
		if ($publication->getUuid() === null) {
			$publication->setUuid(Uuid::v4());
		}

		// Set the uri
		$publication->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.publications.show', ['id' => $publication->getUuid()])));

		return $this->insert(entity: $publication);
	}

	/**
	 * Update an existing Publication from an array of data
	 *
	 * @param int $id The ID of the Publication to update
	 * @param array $object The array of data to update the Publication with
	 * @param bool $updateVersion If we should update the version or not, default = true.
	 *
	 * @return Publication The updated Publication entity
	 */
	public function updateFromArray(int $id, array $object, bool $updateVersion = true, bool $patch = false): Publication
	{
		$publication = $this->find(id: $id);
		// Fallback to create if the publication does not exist
		if ($publication === null) {
			$object['uuid'] = $id;
			return $this->createFromArray($object);
		}

		// Hydrate the publication with the new data
		$publication->hydrate(object: $object);

		// Set the uri
		$publication->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.publications.show', ['id' => $publication->getUuid()])));

		if ($updateVersion === true) {
			// Update the version
			$version = explode('.', $publication->getVersion());
			$version[2] = (int)$version[2] + 1;
			$publication->setVersion(implode('.', $version));
		}

		return $this->update($publication);
	}
}
