<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IURLGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Class AttachmentMapper
 *
 * This class is responsible for mapping Attachment entities to and from the database.
 * It provides methods for finding, creating, updating, and querying Attachment entities.
 *
 * @package OCA\OpenCatalogi\Db
 */
class AttachmentMapper extends QBMapper
{
	/**
	 * Constructor for AttachmentMapper
	 *
	 * @param IDBConnection $db The database connection
	 * @param IURLGenerator $urlGenerator The URL generator
	 */
	public function __construct(IDBConnection $db, IURLGenerator $urlGenerator)
	{
		parent::__construct($db, tableName: 'ocat_attachments');
	}

	/**
	 * Find an Attachment by its ID or UUID
	 *
	 * @param int|string $id The ID or UUID of the Attachment
	 * @return Attachment The found Attachment entity
	 * @throws DoesNotExistException If the entity is not found
	 * @throws MultipleObjectsReturnedException If multiple entities are found
	 */
	public function find($id): Attachment|null
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_attachments')
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
	 * Find multiple Attachments by their IDs or UUIDs
	 *
	 * @param array $ids An array of IDs or UUIDs
	 * @return array An array of found Attachment entities
	 */
	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_attachments')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Find all Attachments with optional limit and offset
	 *
	 * @param int|null $limit Maximum number of results to return
	 * @param int|null $offset Number of results to skip
	 * @return array An array of all found Attachment entities
	 */
	public function findAll(int $limit = null, int $offset = null, array $filters = [], array $sort = [], ?string $search = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_attachments')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	/**
	 * Create a new Attachment from an array of data
	 *
	 * @param array $object An array of Attachment data
	 * @return Attachment The newly created Attachment entity
	 */
	public function createFromArray(array $object): Attachment
	{
		$attachment = new Attachment();

		// Hydrate the attachment with the new data
		$attachment->hydrate(object: $object);

		// Set uuid if not provided
		if ($attachment->getUuid() === null) {
			$attachment->setUuid(Uuid::v4());
		}

		// Set the uri
		$attachment->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.attachments.show', ['id' => $attachment->getUuid()])));

		return $this->insert(entity: $attachment);
	}

	/**
	 * Update an existing Attachment from an array of data
	 *
	 * @param int $id The ID of the Attachment to update
	 * @param array $object An array of updated Attachment data
	 * @param bool $updateVersion If we should update the version or not, default = true.
	 *
	 * @return Attachment The updated Attachment entity
	 * @throws DoesNotExistException If the entity is not found
	 * @throws MultipleObjectsReturnedException|\OCP\DB\Exception If multiple entities are found
	 */
	public function updateFromArray(int $id, array $object, bool $updateVersion = true, bool $patch = false): Attachment
	{
		$attachment = $this->find($id);
		// Fallback to create if the attachment does not exist
		if ($attachment === null) {
			$object['uuid'] = $id;
			return $this->createFromArray($object);
		}

		// Hydrate the attachment with the new data
		$attachment->hydrate($object);

		// Set the uri
		$attachment->setUri($this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.attachments.show', ['id' => $attachment->getUuid()])));

		if ($updateVersion === true) {
			// Update the version
			$version = explode('.', $attachment->getVersion());
			$version[2] = (int)$version[2] + 1;
			$attachment->setVersion(implode('.', $version));
		}

		return $this->update($attachment);
	}
}
