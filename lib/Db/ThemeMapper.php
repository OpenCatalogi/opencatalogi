<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Theme;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ThemeMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'themes');
	}

	public function find(int $id): Theme
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('themes')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('themes')
			->setMaxResults($limit)
			->setFirstResult($offset);

        foreach($filters as $filter => $value) {
            $qb->andWhere($qb->expr()->eq($filter, $qb->createNamedParameter($value)));
        }

        if (!empty($searchConditions)) {
            $qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
            foreach ($searchParams as $param => $value) {
                $qb->setParameter($param, $value);
            }
        }

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Theme
	{
		$theme = new Theme();
		$theme->hydrate(object: $object);

//		var_dump($catalog->getTitle());

		return $this->insert(entity: $catalog);
	}

	public function updateFromArray(int $id, array $object): Theme
	{
		$theme = $this->find($id);
		$theme->hydrate($object);

		return $this->update($theme);
	}
}
