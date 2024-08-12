<?php

namespace OCA\OpenCatalogi\Service;

use DateTime;
use GuzzleHttp\Client;
use OCA\OpenCatalogi\Db\Catalog;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCP\IAppConfig;
use OCP\IURLGenerator;

class DirectoryService
{
	private string $appName = 'opencatalogi';
	private Client $client;

	public function __construct(
		private readonly IURLGenerator $urlGenerator,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService,
		private readonly CatalogMapper $catalogMapper,
		private readonly ListingMapper $listingMapper,
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
			'catalogId' => $catalogId,
			'reference' => '',
		];
	}

	public function registerToExternalDirectory (array $newDirectory = [], ?string $url = null, array &$externalDirectories = []): int
	{
		if($newDirectory !== [] && $url === null) {
			$url = $newDirectory['directory'];
		}


		if($this->config->getValueString($this->appName, 'mongoStorage') !== '1') {
			$catalogi = $this->listingMapper->findAll();
		} else {
			$dbConfig['base_uri'] = $this->config->getValueString('opencatalogi', 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString('opencatalogi', 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString('opencatalogi', 'mongodbCluster');

			$catalogi = $this->objectService->findObjects(filters: ['_schema' => 'directory'], config: $dbConfig)['documents'];
		}

		foreach($catalogi as $catalog) {
			if($catalog instanceof Listing) {
				$catalog = $catalog->jsonSerialize();
			}
			unset($catalog['_id'], $catalog['id'], $catalog['_schema']);

			if($catalog['directory'] !== $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"))) {
				continue;
			}

			$result = $this->client->post(uri: $url, options: ['json' => $catalog, 'http_errors' => false]);
		}

		$externalDirectories = $this->fetchFromExternalDirectory(url: $url);

		if($result !== null) {
			return $result->getStatusCode();
		}
		return 200;

	}

	private function createDirectoryFromResult(array $result): ?array
	{
		unset($result['id']);

		$myDirectory = $this->getDirectoryEntry('');

		if(
			isset($result['directory']) === false
			|| $result['directory'] === $myDirectory['directory']
			|| count($this->listDirectory(filters: ['catalogId' => $result['catalogId'], 'directory' => $result['directory']])) > 0
		) {
			return null;
		}

		if($this->config->getValueString($this->appName, 'mongoStorage') === '1') {
			$dbConfig['base_uri'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbCluster');

			$result['_schema'] = 'directory';

			$returnData = $this->objectService->saveObject(
				data: $result,
				config: $dbConfig
			);
		} else {
			$this->listingMapper->createFromArray($result);
		}

		return $returnData;
	}

	public function fetchFromExternalDirectory(array $directory = [], ?string $url = null): array
	{
		if($directory !== [] && $url === null) {
			$url = $directory['directory'];
		}
 		$result = $this->client->get($url);

		$results = json_decode($result->getBody()->getContents(), true);

		$addedDirectories = [];

		foreach($results['results'] as $record) {
			$addedDirectories[] = $this->createDirectoryFromResult($record);
		}

		return $addedDirectories;
	}

	public function updateToExternalDirectory(): array
	{
		return [];
	}

	public function listDirectory(array $filters = [], int $limit = 30, int $offset = 0): array
	{
		if ($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$filters['catalog_id'] = $filters['catalogId'];
			unset($filters['catalogId']);

			return $this->listingMapper->findAll(limit: $limit, offset: $offset, filters: $filters);
		}
		$filters['_schema'] = 'directory';

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		return $this->objectService->findObjects(filters: $filters, config: $dbConfig)['documents'];
	}

	public function deleteListing(string $catalogId, string $directoryUrl): void
	{
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$results = $this->listingMapper->findAll(filters: ['directory' => $directoryUrl, 'catalog_id' => $catalogId]);

			foreach($results as $result) {
				$this->listingMapper->delete($result);
			}

			return;
		}
		$dbConfig = [
			'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
		];

		$results = $this->objectService->findObjects(filters: ['directory' => $directoryUrl, 'catalogId' => $catalogId, '_schema' => 'directory'], config: $dbConfig);

		foreach($results['documents'] as $result) {
			$this->objectService->deleteObject(filters: ['_id' => $result['_id']], config: $dbConfig);
		}

		return;
	}

	public function directoryExists(string $catalogId): bool
	{
		$directoryUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"));

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$results = $this->listingMapper->findAll(filters: ['directory' => $directoryUrl, 'catalog_id' => $catalogId]);

			return count($results) > 0;
		}
		$dbConfig = [
			'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
		];

		$results = $this->objectService->findObjects(filters: ['directory' => $directoryUrl, 'catalogId' => $catalogId, '_schema' => 'directory'], config: $dbConfig);

		return count($results['documents']) > 0;

	}

	public function listCatalog (array $catalog): array
	{
		$catalogId = $catalog['id'];
		if($catalog['listed'] === false) {
			$this->deleteListing(catalogId: $catalogId, directoryUrl: $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index")),);
			return $catalog;
		} else if ($this->directoryExists($catalogId) === true) {
			return $catalog;
		}


		$listing = $this->getDirectoryEntry(catalogId: $catalogId);

		$listing['title']        = $catalog['title'];
		$listing['organisation'] = $catalog['organisation'];
		$listing['metaData']     = $catalog['metaData'];

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$listing = $this->listingMapper->createFromArray(object: $listing);

			return $catalog;
		}

		try {
			$dbConfig = [
				'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
				'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
				'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
			];

			$listing['_schema'] = 'directory';

			$returnData = $this->objectService->saveObject($listing, $dbConfig);
			return $catalog;
		} catch (\Exception $e) {
			$catalog['listed'] = false;
			return $catalog;
		}

	}
}
