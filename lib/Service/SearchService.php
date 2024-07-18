<?php

namespace OCA\OpenCatalogi\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Symfony\Component\Uid\Uuid;

class SearchService
{
    public $client;
    
	public const BASE_OBJECT = [
		'database'   => 'objects',
		'collection' => 'json',
	];

	public function __construct(
		private readonly ObjectService $objectService,
		private readonly ElasticSearchService $elasticService
	) {
		$this->client = new Client();
	}

	public function mergeFacets(array $existingAggregation, array $newAggregation): array
	{
		$results = [];
		$existingAggregationMapped = [];
		$newAggregationMapped = [];

		foreach($existingAggregation as $value) {
			$existingAggregationMapped[$value['_id']] = $value['count'];
		}


		foreach($newAggregation as $value) {
			if(isset ($existingAggregationMapped[$value['_id']]) === true) {
				$newAggregationMapped[$value['_id']] = $existingAggregationMapped[$value['_id']] + $value['count'];
			} else {
				$newAggregationMapped[$value['_id']] = $value['count'];
			}

		}


		foreach (array_merge(array_diff($existingAggregationMapped, $newAggregationMapped), array_diff($newAggregationMapped, $existingAggregationMapped)) as $key => $value) {
			$results[] = ['_id' => $key, 'count' => $value];
		}

		return $results;
	}

	private function mergeAggregations(?array $existingAggregations, ?array $newAggregations): array
	{
		if($newAggregations === null) {
			return [];
		}


		foreach($newAggregations as $key => $aggregation) {
			if(isset($existingAggregations[$key]) === false) {
				$existingAggregations[$key] = $aggregation;
			} else {
				$existingAggregations[$key] = $this->mergeFacets($existingAggregations[$key], $aggregation);
			}
		}
		return $existingAggregations;
	}

	public function sortResultArray(array $a, array $b): int
	{
		return $a['_score'] <=> $b['_score'];
	}

	/**
	 *
	 */
	public function search(array $parameters, array $elasticConfig, array $dbConfig, array $catalogi = []): array
	{
        
		$localResults = $this->elasticService->searchObject($parameters, $elasticConfig);

		$directory = $this->objectService->findObjects(filters: ['_schema' => 'directory'], config: $dbConfig);

		if(count($directory['documents']) === 0) {
			return $localResults;
		}

		$results = $localResults['results'];
		$aggregations = $localResults['facets'];

		$promises = [];
		foreach($directory['documents'] as $instance) {
			if($instance['default'] === false) {
				continue;
			}
			$url = $instance['search'];
			$promises[] = $this->client->getAsync($url, ['query' => $parameters]);
		}

		$responses = Utils::settle($promises)->wait();

		foreach($responses as $response) {
			if($response['state'] === 'fulfilled') {
				$responseData = json_decode(
					json: $response['value']->getBody()->getContents(),
					associative: true
				);

				$results = array_merge(
					$results,
					$responseData['results']
				);

				usort($results, [$this, 'sortResultArray']);

				$aggregations = $this->mergeAggregations($aggregations, $responseData['facets']);
			}
		}

		return ['results' => $results, 'facets' => $aggregations];
	}

}
