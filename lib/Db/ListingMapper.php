<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\Organization;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\Exception;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

/**
 * Class ListingMapper
 *
 * This class is responsible for mapping Listing entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Listing entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class ListingMapper extends QBMapper
{
	/**
	 * Constructor for ListingMapper
	 *
	 * @param IDBConnection $db The database connection
	 */
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_listings');
	}

	/**
	 * Find a Listing by its ID or UUID
	 *
	 * @param int|string $id The ID or UUID of the Listing
	 * @return Listing The found Listing entity
	 */
	public function find($id): Listing|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
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
	 * Find multiple Listings by their IDs or UUIDs
	 *
	 * @param array $ids An array of IDs or UUIDs
	 * @return array An array of found Listing entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Custom method to find a single entity, supporting JOINs
	 *
	 * @param IQueryBuilder $query The query builder object
	 * @return Entity The found entity
	 * @throws \OCP\AppFramework\Db\DoesNotExistException if the item does not exist
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one item exists
	 */
	protected function findEntityCustom(IQueryBuilder $query): Entity {
		return $this->mapRowToEntityCustom($this->findOneQuery($query));
	}

	/**
	 * Custom method to map a database row to an entity, supporting JOINs
	 *
	 * @param array $row The database row
	 * @return Entity The mapped entity
	 */
	protected function mapRowToEntityCustom(array $row): Entity {
		unset($row['DOCTRINE_ROWNUM']); // remove doctrine/dbal helper column

		// Map the Organization fields to a sub-array
		$organizationData = [
			'id' => $row['organization_id'] ?? null,
			'title' => $row['organization_title'] ?? null,
			'summary' => $row['organization_summary'] ?? null,
			'description' => $row['organization_description'] ?? null,
			'image' => $row['organization_image'] ?? null,
			'oin' => $row['organization_oin'] ?? null,
			'tooi' => $row['organization_tooi'] ?? null,
			'rsin' => $row['organization_rsin'] ?? null,
			'pki' => $row['organization_pki'] ?? null,
		];

		$organizationIsEmpty = true;
		foreach ($organizationData as $key => $value) {
			if ($value !== null) {
				$organizationIsEmpty = false;
			}

			if (array_key_exists("organization_$key", $row) === true) {
				unset($row["organization_$key"]);
			}
		}

		$row['organization'] = $organizationIsEmpty === true ? null : json_encode(Organization::fromRow($organizationData)->jsonSerialize());

		return \call_user_func($this->entityClass .'::fromRow', $row);
	}

	/**
	 * Custom method to find multiple entities, supporting JOINs
	 *
	 * @param IQueryBuilder $query The query builder object
	 * @return array An array of found entities
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

	/**
	 * Find all Listings with optional filtering and searching
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @param array|null $filters Associative array of filters
	 * @param array|null $searchConditions Array of search conditions
	 * @param array|null $searchParams Array of search parameters
	 * @return array An array of found Listing entities
	 */
	public function findAll(?int $limit = null, ?int $offset = null, array $filters = [], array $sort = [], ?string $search = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
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

		// Use the existing findEntities method to fetch and map the results
		return $this->findEntities($qb);
	}

	/**
	 * Find a Listing by its catalog ID and directory
	 *
	 * @param string $catalog The catalog ID to search for, should be a UUID
	 * @param string $directory The directory to search for, should be a URL
	 *
	 * @return Listing|null The found Listing entity or null if not found
	 * @throws MultipleObjectsReturnedException|Exception
	 */
	public function findByCatalogIdAndDirectory(string $catalog, string $directory): ?Listing
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
			->where($qb->expr()->eq('uuid', $qb->createNamedParameter($catalog)))
			->andWhere($qb->expr()->eq('directory', $qb->createNamedParameter($directory)))
			->setMaxResults(1);

		try {
			return $this->findEntity($qb);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return null;
		}
	}

	/**
	 * Create a new Listing from an array of data
	 *
	 * @param array $object An array of Listing data
	 * @return Listing The created Listing entity
	 */
	public function createFromArray(array $object): Listing
	{
		$listing = new Listing();
		$listing->hydrate(object: $object);

		// Set UUID if not provided
		if ($listing->getUuid() === null) {
			$listing->setUuid(Uuid::v4());
		}

		return $this->insert(entity: $listing);
	}

	/**
	 * Update an existing Listing from an array of data
	 *
	 * @param int|string $id The ID or UUID of the Listing to update
	 * @param array $object An array of updated Listing data
	 * @param bool $updateVersion If we should update the version or not, default = true.
	 *
	 * @return Listing The updated Listing entity
	 * @throws Exception
	 */
	public function updateFromArray(int|string $id, array $object, bool $updateVersion = true, bool $patch = false): Listing
	{
		$listing = $this->find($id);
		// Fallback to create if the listing does not exist
		if ($listing === null) {
			$object['uuid'] = $id;
			return $listing = $this->createFromArray($object);
		}

		$listing->hydrate($object);

		if ($updateVersion === true) {
			// Update the version
			$version = explode('.', $listing->getVersion());
			$version[2] = (int)$version[2] + 1;
			$listing->setVersion(implode('.', $version));
		}

		return $this->update($listing);
	}

	/**
	 * Get all unique directories
	 *
	 * @return array An array of unique directory strings
	 */
	public function getAllUniqueDirectories(): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('DISTINCT directory')
			->from('ocat_listings')
			->where($qb->expr()->isNotNull('directory'));

		$result = $qb->executeQuery();
		$directories = $result->fetchAll(\PDO::FETCH_COLUMN);
		$result->closeCursor();

		return $directories;
	}
}
