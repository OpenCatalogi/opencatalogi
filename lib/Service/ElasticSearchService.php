<?php
/**
 * Service for managing interactions with Elasticsearch.
 *
 * Provides functionality for indexing, updating, deleting, and searching objects in Elasticsearch,
 * as well as processing and formatting query results and aggregations.
 *
 * @category Service
 * @package  OCA\OpenCatalogi\Service
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2024 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git_id>
 *
 * @link https://www.OpenCatalogi.nl
 */

namespace OCA\OpenCatalogi\Service;



use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\Uid\Uuid;

/**
 * Service for managing interactions with Elasticsearch.
 *
 * Provides functionality for indexing, updating, deleting, and searching objects in Elasticsearch,
 * as well as processing and formatting query results and aggregations.
 */

class ElasticSearchService
{


    private function getClient(array $config): Client
    {
        $uri    = $config['location'];
        $apiKey = explode(separator: ':', string: base64_decode(string: $config['key']));

        return ClientBuilder::create()
            ->setHosts([$uri])
            ->setApiKey(apiKey: $apiKey[1], id: $apiKey[0])
            ->build();

    }//end getClient()


    /**
     * Add an object to ElasticSearch
     *
     * @param array $object The object to add to the data store.
     * @param array $config The configuration of ElasticSearch.
     *
     * @return array
     */
    public function addObject(array $object, array $config): array
    {
        $client = $this->getClient(config: $config);

        if (isset($object['_id']) === true) {
            unset($object['_id']);
        }

        try {
            $result = $client->index(
                params: [
                    'index' => $config['index'],
                    'id'    => $object['id'],
                    'body'  => $object,
                ]
            );
        } catch (\Exception $exception) {
            return [
                'exception' => [
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString(),
                ],
            ];
        }

        return $client->get(
            params: [
                'index' => $config['index'],
                'id'    => $object['id'],
            ]
        )['_source'];

    }//end addObject()


    public function removeObject(string $id, array $config): array
    {
        $client = $this->getClient(config: $config);

        try {
            $client->delete(
                params: [
                    'index' => $config['index'],
                    'id'    => $id,
                ]
            );
            return [];
        } catch (\Exception $exception) {
            return [
                'exception' => [
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString(),
                ],
            ];
        }

    }//end removeObject()


    public function updateObject(string $id, array $object, array $config): array
    {
        $client = $this->getClient(config: $config);

        if (isset($object['_id']) === true) {
            unset($object['_id']);
        }

        try {
            $client->index(
                params: [
                    'index' => $config['index'],
                    'id'    => $id,
                    'body'  => ['doc' => $object],
                ]
            );
            return [];
        } catch (\Exception $exception) {
            return [
                'exception' => [
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString(),
                ],
            ];
        }

    }//end updateObject()


    public function parseFilter(string $name, array|string $filter): array
    {

        if (is_array($filter) === false) {
            return ['match' => [$name => $filter]];
        }

        foreach ($filter as $key => $value) {
            switch ($key) {
                case 'regexp':
                case 'like':
                    if (preg_match("/^\/.+\/[a-z]*$/i", $value) !== false) {
                        return ['regexp' => [$name => strtolower($value)]];
                    } else {
                        return ['match' => [$name => $value]];
                    }

                case '>=':
                case 'after':
                    return ['range' => [$key => ['gte' => $value]]];
                case '>':
                case 'strictly_after':
                    return ['range' => [$key => ['gt' => $value]]];
                case '<=':
                case 'before':
                    return ['range' => [$key => ['lte' => $value]]];
                case '<':
                case 'strictly_before':
                    return ['range' => [$key => ['lt' => $value]]];
                default:
                    return ['match' => [$name => $value]];
            }//end switch
        }//end foreach

        return ['match' => [$name => $filter]];

    }//end parseFilter()


    public function parseFilters(array $filters): array
    {
        $body = [
            'query' => [
                'bool' => [
                    'must' => [],
                ],
            ],
        ];

        if (isset($filters['_search']) === true) {
            $body['query']['bool']['must'][] = ['query_string' => ['query' => '*'.$filters['_search'].'*']];
        }

        if (isset($filters['_queries']) === true) {
            foreach ($filters['_queries'] as $query) {
                $body['runtime_mappings'][$query] = ['type' => 'keyword'];
                $body['aggs'][$query]             = ['terms' => ['field' => $query]];
            }
        }

        if (isset($filters['_catalogi']) === true) {
            $body['query']['bool']['must'][] = [
                'match' => [
                    'catalogi._id' => [
                        'query'    => implode(separator: " ", array: $filters['_catalogi']),
                        'operator' => 'OR',
                    ],
                ],
            ];
        }

        if (isset($filters['_limit']) === true) {
            $body['size'] = (int) $filters['_limit'];
            unset($filters['_limit']);
        }

        if (isset($filters['_page']) === true) {
            if (isset($body['size']) === true) {
                $body['from'] = ($body['size'] * ($filters['_page'] - 1));
            }

            unset($filters['_page']);
        }

        if (isset($filters['_order']) === true) {
            $body['sort'] = [];
            foreach ($filters['_order'] as $key => $value) {
                $body['sort'][] = [$key => $value];
            }

            unset($filters['_order']);
        }

        unset($filters['_search'], $filters['_queries'], $filters['_catalogi']);

        foreach ($filters as $name => $filter) {
            $body['query']['bool']['must'][] = $this->parseFilter($name, $filter);
        }

        return $body;

    }//end parseFilters()


    public function formatResults(array $hit): array
    {
        $source = $hit['_source'];

        unset($hit['_source']);
        $hit = array_merge($hit, $source);

        return $hit;

    }//end formatResults()


    /**
     * Rename the items in an aggregation bucket according to the response standard for aggregations.
     *
     * @param array $bucketItem The item to rewrite
     *
     * @return array The rewritten array.
     */
    public function renameBucketItems(array $bucketItem): array
    {
        return [
            '_id'   => $bucketItem['key'],
            'count' => $bucketItem['doc_count'],
        ];

    }//end renameBucketItems()


    /**
     * Map aggregation results to comply to the existing standard for aggregation results.
     *
     * @param array $result The result to map.
     *
     * @return array The mapped result.
     */
    public function mapAggregationResults(array $result): array
    {
        $buckets = $result['buckets'];

        $result = array_map([$this, 'renameBucketItems'], $buckets);

        return $result;

    }//end mapAggregationResults()


    public function searchObject(array $filters, array $config, int &$totalResults=0): array
    {
        $body = $this->parseFilters(filters: $filters);

        $client = $this->getClient(config: $config);
        $result = $client->search(
            params: [
                'index' => $config['index'],
                'body'  => $body,
            ]
        );

        $totalResults = $result['hits']['total']['value'];

        $return = ['results' => array_map(callback: [$this, 'formatResults'], array: $result['hits']['hits'])];
        if (isset($result['aggregations']) === true) {
            $return['facets'] = array_map([$this, 'mapAggregationResults'], $result['aggregations']);
        } else {
            $return['facets'] = [];
        }

        return $return;

    }//end searchObject()


}//end class
