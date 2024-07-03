<?php

namespace OCA\OpenCatalog\Service;



use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Symfony\Component\Uid\Uuid;

class ElasticSearchService
{
	private function getClient(array $config): Client
	{
		$uri    = $config['location'];
		$apiKey = explode(separator: ':', string: base64_decode(string: $config['key']));

		$client = ClientBuilder::create()
			->setHosts([$uri])
			->setApiKey(apiKey: $apiKey[1],id: $apiKey[0])
			->build();

		return $client;
	}


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

		if(isset($object['_id']) === true) {
			unset($object['_id']);
		}

		try {
			$result = $client->index(params: [
				'index' => $config['index'],
				'id'	=> $object['id'],
				'body'	=> $object,
			]);
		} catch (\Exception $exception) {
			return ['exception' => ['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]];
		}

		return $client->get(params: [
			'index' => $config['index'],
			'id'	=> $object['id'],
		])['_source'];

	}

	public function removeObject(string $id, array $config): array
	{
		$client = $this->getClient(config: $config);

		try {
			$client->delete(params: [
				'index' => $config['index'],
				'id'    => $id,
			]);
			return [];
		} catch (\Exception $exception) {
			return ['exception' => ['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]];
		}
	}

	public function updateObject(string $id, array $object, array $config): array
	{
		$client = $this->getClient(config: $config);

		if(isset($object['_id']) === true) {
			unset($object['_id']);
		}

		try {
			$client->update(params: [
				'index' => $config['index'],
				'id'    => $id,
				'body'  => ['doc' => $object],
			]);
			return [];
		} catch (\Exception $exception) {
			return ['exception' => ['message' => $exception->getMessage(), 'trace' => $exception->getTraceAsString()]];
		}
	}

	private function parseFilters (array $filters): array
	{
		$body = [
			'query' => [
				'bool' => [
					'must' => []
				]
			]
		];

		if(isset($filters['_search']) === true) {
			$body['query']['bool']['must'][] = ['query_string' => ['query' => '*'.$filters['_search'].'*']];
		}

		unset($filters['_search']);

		foreach ($filters as $name => $filter) {
			$body['query']['bool']['must'][] = ['match' => [$name => $filter]];
		}

		return $body;
	}

	private function formatResults(array $hit): array
	{
		$source = $hit['_source'];

		unset($hit['_source']);
		$hit = array_merge($hit, $source);

		return $hit;

	}//end formatResults()

	public function searchObject(array $filters, array $config): array
	{
		$body = $this->parseFilters(filters: $filters);

		$client = $this->getClient(config: $config);

		$result = $client->search(params: [
			'index' => $config['index'],
			'body'  => $body
		]);

		return array_map(callback: [$this, 'formatResults'], array: $result['hits']['hits']);
	}
}
