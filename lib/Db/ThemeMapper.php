<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Theme;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Class ThemeMapper
 *
 * This class is responsible for mapping Theme entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Theme entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class ThemeMapper extends QBMapper
{
	/**
	 * Constructor for ThemeMapper
	 *
	 * @param IDBConnection $db The database connection
	 * @param IURLGenerator $urlGenerator The URL generator
	 */
	public function __construct(IDBConnection $db, IURLGenerator $urlGenerator)
	{
		parent::__construct($db, tableName: 'ocat_themes');
	}

	/**
	 * Find a Theme by its ID
	 *
	 * @param int $id The ID of the Theme
	 * @return Theme The found Theme entity
	 * @throws DoesNotExistException If the entity is not found
	 * @throws MultipleObjectsReturnedException If multiple entities are found
	 */
	public function find(int $id): Theme|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_themes')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		try {
			return $this->findEntity($qb);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return null;
		}
	}

	/**
	 * Find multiple Themes by their IDs
	 *
	 * @param array $ids An array of Theme IDs
	 * @return array An array of found Theme entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_themes')
			->where($qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Find all Themes with optional filtering and searching
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @param array|null $filters Associative array of filters
	 * @param array|null $searchConditions Array of search conditions
	 * @param array|null $searchParams Array of search parameters
	 * @return array An array of found Theme entities
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
			->from('ocat_themes')
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
	 * Create a new Theme from an array of data
	 *
	 * @param array $object An array of Theme data
	 * @return Theme The newly created Theme entity
	 */
	public function createFromArray(array $object): Theme
	{
		$theme = new Theme();
		$theme->hydrate(object: $object);

		// Set uuid if not provided
		if ($theme->getUuid() === null) {
			$theme->setUuid(Uuid::v4());
		}

		// Set the uri
		$theme->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.themes.show', ['id' => $theme->getUuid()])));

		return $this->insert(entity: $theme);
	}

	/**
	 * Update an existing Theme from an array of data
	 *
	 * @param int $id The ID of the Theme to update
	 * @param array $object An array of updated Theme data
	 * @param bool $updateVersion If we should update the version or not, default = true.
	 *
	 * @return Theme The updated Theme entity
	 * @throws DoesNotExistException If the entity is not found
	 * @throws MultipleObjectsReturnedException|\OCP\DB\Exception If multiple entities are found
	 */
	public function updateFromArray(int $id, array $object, bool $updateVersion = true, bool $patch = false): Theme
	{
		$theme = $this->find($id);
		// Fallback to create if the theme does not exist
		if ($theme === null) {
			$object['uuid'] = $id;
			return $this->createFromArray($object);
		}

		// Hydrate the theme with the new data
		$theme->hydrate($object);
		
		// Set the uri
		$theme->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.themes.show', ['id' => $theme->getUuid()])));


		if ($updateVersion === true) {
			// Update the version
			$version = explode('.', $theme->getVersion());
			$version[2] = (int)$version[2] + 1;
			$theme->setVersion(implode('.', $version));
		}

		return $this->update($theme);
	}
}
