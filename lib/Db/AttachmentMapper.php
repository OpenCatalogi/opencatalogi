<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class AttachmentMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'attachments');
	}

	public function find(int $id): Attachment
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('attachments')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll($limit = null, $offset = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('attachments')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Attachment
	{
		$attachment = new Attachment();
		$attachment->hydrate(object: $object);

//		var_dump($attachment->getTitle());

		return $this->insert(entity: $attachment);
	}

	public function updateFromArray(int $id, array $object): Attachment
	{
		$attachment = $this->find($id);
		$attachment->hydrate($object);

		return $this->update($attachment);
	}
}
