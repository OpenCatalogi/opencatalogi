<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Page;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

/**
 * Class PageMapper
 *
 * This class is responsible for mapping Page entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Page entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class PageMapper extends QBMapper
{
	/**
	 * Constructor for PageMapper
	 *
	 * @param IDBConnection $db The database connection
	 */
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_pages');
	}

	/**
	 * Find a Page by its ID or UUID
	 *
	 * @param int|string $id The ID or UUID of the Page
	 * @return Page The found Page entity
	 * @throws DoesNotExistException If the entity is not found
	 * @throws MultipleObjectsReturnedException If multiple entities are found
	 */
	public function find($id): Page|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_pages')
			->where($qb->expr()->orX(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)),
				$qb->expr()->eq('uuid', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR)),
				$qb->expr()->eq('slug', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR))
			));

		try {
			return $this->findEntity($qb);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return null;
		}
	}

	/**
	 * Find multiple Pages by their IDs or UUIDs
	 *
	 * @param array $ids An array of IDs or UUIDs
	 * @return array An array of found Page entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_pages')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Find all Pages with optional limit and offset
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @return array An array of all found Page entities
	 */
	public function findAll(int $limit = null, int $offset = null, array $filters = [], array $sort = [], ?string $search = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_pages')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	/**
	 * Create a new Page from an array of data
	 *
	 * @param array $object An array of Page data
	 * @return Page The newly created Page entity
	 */
	public function createFromArray(array $object, array $extend = []): Page
	{
		$page = new Page();
		$page->hydrate(object: $object);

		// Set uuid if not provided
		if ($page->getUuid() === null) {
			$page->setUuid(Uuid::v4());
		}

		// Generate slug from name if not provided
		if ($page->getSlug() === null && $page->getName() !== null) {
			// Convert to lowercase and replace spaces with dashes
			$slug = strtolower($page->getName());
			$slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
			$slug = preg_replace('/-+/', '-', $slug);
			$slug = trim($slug, '-');
			$page->setSlug($slug);
		}

		return $this->insert(entity: $page);
	}

	/**
	 * Update an existing Page from an array of data
	 *
	 * @param int $id The ID of the Page to update
	 * @param array $object An array of updated Page data
	 * @return Page The updated Page entity
	 * @throws DoesNotExistException If the entity is not found
	 * @throws MultipleObjectsReturnedException|\OCP\DB\Exception If multiple entities are found
	 */
	public function updateFromArray(int $id, array $object, array $extend, bool $updateVersion = false, bool $patch = false): Page
	{
		$page = $this->find($id);
		// Fallback to create if the page does not exist
		if ($page === null) {
			$object['uuid'] = $id;
			return $this->createFromArray($object);
		}

		$page->hydrate($object);

		return $this->update($page);
	}
}
