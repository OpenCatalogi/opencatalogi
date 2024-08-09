<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\Organisation;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class ListingMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'listings');
	}

	public function find(int $id): Listing
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('listings')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

    /**
     *  CUSTOM FOR JOINS
     */
    protected function mapRowToEntityCustom(array $row): Entity {
		unset($row['DOCTRINE_ROWNUM']); // remove doctrine/dbal helper column

        // Map the Organisation fields to a sub-array
        $organisationData = [
            'id' => $row['organisation_id'] ?? null,
            'title' => $row['organisation_title'] ?? null,
            'summary' => $row['organisation_summary'] ?? null,
            'description' => $row['organisation_description'] ?? null,
            'image' => $row['organisation_image'] ?? null,
            'oin' => $row['organisation_oin'] ?? null,
            'tooi' => $row['organisation_tooi'] ?? null,
            'rsin' => $row['organisation_rsin'] ?? null,
            'pki' => $row['organisation_pki'] ?? null,
        ];

        $organisationIsEmpty = true;
        foreach ($organisationData as $key => $value) {
            if ($value !== null) {
                $organisationIsEmpty = false;
                break;
            }
        }

        $row['organisation'] = $organisationIsEmpty === true ? null : Organisation::fromRow($organisationData);

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

    public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
    {
        $qb = $this->db->getQueryBuilder();
    
        $qb->select(
                'l.*', 
                'o.id AS organisation_id', 
                'o.title AS organisation_title', 
                'o.summary AS organisation_summary',
                'o.description AS organisation_description',
                'o.image AS organisation_image', 
                'o.oin AS organisation_oin', 
                'o.tooi AS organisation_tooi', 
                'o.rsin AS organisation_rsin', 
                'o.pki AS organisation_pki'
            )
            ->from('listings', 'l')
            ->leftJoin('l', 'organizations', 'o', 'l.organisation = o.id')
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
    
        // Apply search conditions
        if (!empty($searchConditions)) {
            $qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
            foreach ($searchParams as $param => $value) {
                $qb->setParameter($param, $value);
            }
        }
    
        // Use the existing findEntities method to fetch and map the results
        return $this->findEntitiesCustom($qb);
    }

	public function createFromArray(array $object): Listing
	{
		$listing = new Listing();
		$listing->hydrate(object: $object);

//		var_dump($listing->getTitle());

		return $this->insert(entity: $listing);
	}

	public function updateFromArray(int $id, array $object): Listing
	{
		$listing = $this->find($id);
		$listing->hydrate($object);

		return $this->update($listing);
	}
}
