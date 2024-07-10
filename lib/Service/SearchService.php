<?php

namespace OCA\OpenCatalogi\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use Symfony\Component\Uid\Uuid;

class SearchService
{

	public const BASE_OBJECT = [
		'database'   => 'objects',
		'collection' => 'json',
	];

	public function __construct(
		private readonly ObjectService $objectService
	) {

	}

	/**
	 * Gets a guzzle client based upon given config.
	 *
	 * @param array $config The config to be used for the client.
	 * @return Client
	 */
	private function getClient(
		array $config,
	): Client
	{
		$guzzleConf = $config;

		return new Client($config);
	}

	/**
	 *
	 */
	public function search(array $parameters, array $elasticConfig, array $dbConfig): array
	{
		$elasticService = new ElasticSearchService();
		$localResults = $elasticService->searchObject($parameters, $elasticConfig);

		$client    = $this->getClient(config: []);
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
			$promises[] = $client->getAsync($url, ['query' => $parameters]);
		}

		$responses = Utils::settle($promises)->wait();

		foreach($responses as $response) {
			if($response['state'] === 'fulfilled') {
				$results = array_merge(
					$results,
					json_decode(
						json: $response['value']->getBody()->getContents(),
						associative: true
					)['results']
				);
			}
		}

		return ['results' => $results, 'facets' => $aggregations];
	}

}
