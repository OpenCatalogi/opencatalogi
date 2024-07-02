<?php

namespace OCA\OpenCatalog\Service;

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
	public function search(array $parameters, $dbConfig): array
	{
		$client    = $this->getClient(config: []);
		$directory = $this->objectService->findObjects(filters: ['_schema' => 'directory'], config: $dbConfig);

		$promises = [];
		foreach($directory as $instance) {
			$url = $instance['url'];
			$promises[] = $client->getAsync($url.'/publications');
		}

		$responses = Utils::settle($promises);

		
	}

}
