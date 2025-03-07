<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Organization;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Class OrganizationMapper
 *
 * This class is responsible for mapping Organization entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Organization entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class OrganizationMapper extends QBMapper
{
	/**
	 * Constructor for OrganizationMapper
	 *
	 * @param IDBConnection $db The database connection
	 * @param IURLGenerator $urlGenerator The URL generator
	 */
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_organizations');
	}

	/**
	 * Find an Organization by its ID or UUID
	 *
	 * @param int|string $id The ID or UUID of the Organization
	 * @return Organization The found Organization entity
	 * @throws \OCP\AppFramework\Db\DoesNotExistException If the entity is not found
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException If multiple entities are found
	 */
	public function find($id): Organization|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_organizations')
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
	 * Find multiple Organizations by their IDs or UUIDs
	 *
	 * @param array $ids An array of IDs or UUIDs
	 * @return array An array of found Organization entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_organizations')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Find all Organizations with optional limit, offset, filters, and search conditions
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @param array|null $filters Associative array of filters to apply
	 * @param array|null $searchConditions Array of search conditions
	 * @param array|null $searchParams Array of search parameters
	 * @return array An array of all found Organization entities
	 */
	public function findAll(?int $limit = null, ?int $offset = null, array $filters = [], array $sort = [], ?string $search = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_organizations')
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
	 * Create a new Organization from an array of data
	 *
	 * @param array $object An array of Organization data
	 * @return Organization The newly created Organization entity
	 */
	public function createFromArray(array $object): Organization
	{
		$organization = new Organization();
		$organization->hydrate(object: $object);

		// Set uuid if not provided
		if ($organization->getUuid() === null) {
			$organization->setUuid(Uuid::v4());
		}

		// Set the uri
		$organization->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.organizations.show', ['id' => $organization->getUuid()])));

		return $this->insert(entity: $organization);
	}

	/**
	 * Update an existing Organization from an array of data
	 *
	 * @param int $id The ID of the Organization to update
	 * @param array $object An array of updated Organization data
	 * @param bool $updateVersion If we should update the version or not, default = true.
	 *
	 * @return Organization The updated Organization entity
	 */
	public function updateFromArray(int $id, array $object, bool $updateVersion = true, bool $patch = false): Organization
	{
		$organization = $this->find($id);
		// Fallback to create if the organization does not exist
		if ($organization === null) {
			$object['uuid'] = $id;
			return $this->createFromArray($object);
		}

		// Hydrate the organization with the new data
		$organization->hydrate($object);

		// Set the uri
		$organization->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.organizations.show', ['id' => $organization->getUuid()])));

		if ($updateVersion === true) {
			// Update the version
			$version = explode('.', $organization->getVersion());
			$version[2] = (int)$version[2] + 1;
			$organization->setVersion(implode('.', $version));
		}

		return $this->update($organization);
	}
}
