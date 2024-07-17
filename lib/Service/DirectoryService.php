<?php

namespace OCA\OpenCatalogi\Service;

use DateTime;
use GuzzleHttp\Client;
use OCP\IAppConfig;
use OCP\IURLGenerator;

class DirectoryService
{
	private Client $client;

	public function __construct(
		private readonly IURLGenerator $urlGenerator,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService,
	)
	{
		$this->client = new Client([]);
	}

	private function getDirectoryEntry(string $catalogId): array
	{
		$now = new DateTime();
		return [
			'title' => '',
			'summary' => '',
			'description' => '',
			'search'	=> $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(routeName:"opencatalogi.search.index")),
			'directory'	=> $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index")),
			'metadata'	=> '',
			'status'	=> '',
			'lastSync'	=> $now->format(format: 'c'),
			'default'	=> true,
			'catalogId' => $catalogId
		];
	}

	public function registerToExternalDirectory (array $newDirectory): int
	{
		$dbConfig['base_uri'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbCluster');

		$catalogi = $this->objectService->findObjects(filters: ['_schema' => 'catalog'], config: $dbConfig)['documents'];

		foreach($catalogi as $catalog) {
			$directory = $this->getDirectoryEntry($catalog['id']);
			$result = $this->client->post(uri: $newDirectory['directory'], options: ['json' => $directory, 'http_errors' => false]);
		}

		$externalDirectories = $this->fetchFromExternalDirectory($newDirectory);

		return $result->getStatusCode();

	}

	private function createDirectoryFromResult(array $result): ?array
	{
		$myDirectory = $this->getDirectoryEntry('');

		if(isset($result['directory']) === false || $result['directory'] === $myDirectory['directory']) {
			return null;
		}

		$dbConfig['base_uri'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbCluster');

		$result['_schema'] = 'directory';

		$returnData = $this->objectService->saveObject(
			data: $result,
			config: $dbConfig
		);

		$this->registerToExternalDirectory(newDirectory: $result);

		return $returnData;
	}

	public function fetchFromExternalDirectory(array $directory): array
	{
		$result = $this->client->get($directory['directory']);

		$results = json_decode($result->getBody()->getContents());

		foreach($results['results'] as $record) {
			$this->createDirectoryFromResult($record);
		}

		return $results['results'];
	}

	public function updateToExternalDirectory(): array
	{

	}
}
