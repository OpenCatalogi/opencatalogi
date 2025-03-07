<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Catalog;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Class CatalogMapper
 *
 * This class is responsible for mapping Catalog entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Catalog entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class CatalogMapper extends QBMapper
{
	/**
	 * Constructor for CatalogMapper
	 *
	 * @param IDBConnection $db The database connection
	 * @param IURLGenerator $urlGenerator The URL generator
	 */
	public function __construct(IDBConnection $db, IURLGenerator $urlGenerator)
	{
		parent::__construct($db, tableName: 'ocat_catalogi');
	}

	/**
	 * Find a Catalog by its ID or UUID
	 *
	 * @param int|string $id The ID or UUID of the Catalog
	 * @return Catalog The found Catalog entity
	 */
	public function find($id): Catalog|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_catalogi')
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
	 * Find multiple Catalogs by their IDs or UUIDs
	 *
	 * @param array $ids An array of IDs or UUIDs
	 * @return array An array of found Catalog entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_catalogi')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Find all Catalogs with optional filtering and searching
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @param array|null $filters Associative array of filters
	 * @param array|null $searchConditions Array of search conditions
	 * @param array|null $searchParams Array of search parameters
	 * @return array An array of found Catalog entities
	 */
	public function findAll(
		?int $limit = null,
		?int $offset = null,
		array $filters = [],
		array $sort = [],
		?string $search = null
	): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_catalogi')
			->setMaxResults($limit)
			->setFirstResult($offset);

		// Apply filters
		foreach ($filters as $filter => $value) {
			if ($value === 'IS NOT NULL') {
				$qb->andWhere($qb->expr()->isNotNull($filter));
			} elseif ($value === 'IS NULL') {
				$qb->andWhere($qb->expr()->isNull($filter));
			} else {
				$qb->andWhere($qb->expr()->eq($filter, $qb->createNamedParameter($value)));
			}
		}

		return $this->findEntities(query: $qb);
	}

	/**
	 * Create a new Catalog from an array of data
	 *
	 * @param array $object An array of Catalog data
	 * @return Catalog The newly created Catalog entity
	 */
	public function createFromArray(array $object): Catalog
	{
		$catalog = new Catalog();
		$catalog->hydrate(object: $object);

		// Set uuid if not provided
		if ($catalog->getUuid() === null) {
			$catalog->setUuid(Uuid::v4());
		}

		// Set the uri
		$catalog->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.catalogs.show', ['id' => $catalog->getUuid()])));

		return $this->insert(entity: $catalog);
	}

	/**
	 * Update an existing Catalog from an array of data
	 *
	 * @param int $id The ID of the Catalog to update
	 * @param array $object An array of updated Catalog data
	 * @param bool $updateVersion If we should update the version or not, default = true.
	 *
	 * @return Catalog The updated Catalog entity
	 */
	public function updateFromArray(int $id, array $object, bool $updateVersion = true, bool $patch = false): Catalog
	{
		$catalog = $this->find($id);
		// Fallback to create if the catalog does not exist
		if ($catalog === null) {
			$object['uuid'] = $id;
			return $this->createFromArray($object);
		}

		// Hydrate the catalog with the new data
		$catalog->hydrate($object);

		// Set the uri
		$catalog->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.catalogs.show', ['id' => $catalog->getUuid()])));

		if ($updateVersion === true) {
			// Update the version
			$version = explode('.', $catalog->getVersion());
			$version[2] = (int)$version[2] + 1;
			$catalog->setVersion(implode('.', $version));
		}

		return $this->update($catalog);
	}
}
