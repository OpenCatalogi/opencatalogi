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
			'search'	=> $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.search.index")),
			'directory'	=> $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index")),
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


		if($this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1') {
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

			if($catalog['directory'] !== $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"))) {
				continue;
			}

			$result = $this->client->post(uri: $url, options: ['json' => $catalog, 'http_errors' => false]);
		}

		$externalDirectories = $this->fetchFromExternalDirectory(url: $url, update: true);

		if($result !== null) {
			return $result->getStatusCode();
		}
		return 200;

	}

	private function createDirectoryFromResult(array $result, bool $update = false): ?array
	{
		unset($result['id']);

		$myDirectory = $this->getDirectoryEntry(catalogId: '');

		if(
			isset($result['directory']) === false
			|| $result['directory'] === $myDirectory['directory']
			|| (
				count($this->listDirectory(filters: ['catalogId' => $result['catalogId'], 'directory' => $result['directory']])) > 0
				&& $update === false
			)
		) {
			return null;
		} else if (count($this->listDirectory(filters: ['catalogId' => $result['catalogId'], 'directory' => $result['directory']])) > 0 && $update === true) {
			$listing = $this->listDirectory(filters: ['catalogId' => $result['catalogId'], 'directory' => $result['directory']])[0];

			if($listing instanceof Listing) {
				$listing = $listing->jsonSerialize();
			}

			$id = $listing['id'];
		}

		if($this->config->getValueString($this->appName, 'mongoStorage') === '1') {
			$dbConfig['base_uri'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: 'opencatalogi', key: 'mongodbCluster');

			$result['_schema'] = 'directory';

			if(isset($id) === true) {
				$this->objectService->updateObject(
					filters: ['id' => $id],
					update: $result,
					config: $dbConfig
				);
			}

			return $this->objectService->saveObject(
				data: $result,
				config: $dbConfig
			);
		} else {
			if(isset($id) === true) {
				return $this->listingMapper->updateFromArray(id: $id, object: $result)->jsonSerialize();
			}
			return $this->listingMapper->createFromArray(object: $result)->jsonSerialize();
		}
	}

	/**
	 * array_map function for fetching a directory for a listing.
	 *
	 * @param Listing $listing
	 * @return string
	 */
	private function getDirectory(Listing $listing): string
	{
		return $listing->getDirectory();
	}

	/**
	 * Get all directories to scan.
	 *
	 * @return array
	 */
	private function getDirectories(): array
	{
		$listings = $this->listingMapper->findAll();

		$directories = array_map(callback: [$this, 'getDirectory'], array: $listings);

		return array_unique(array: $directories);
	}

	/**
	 * Run a synchronisation based on cron
	 *
	 * @return array
	 */
	public function doCronSync(): array {

		$results = [];
		$directories = $this->getDirectories();
		foreach($directories as $key=>$directory){
			$result = $this->fetchFromExternalDirectory(url: $directory,  update: true);
			$results = array_merge_recursive($results, $result);
		}

		return $results;
	}

	// Get or update the data for an specifi exernal directory
	public function fetchFromExternalDirectory(array $directory = [], ?string $url = null, bool $update = false): array
	{
		if($directory !== [] && $url === null) {
			$url = $directory['directory'];
		}
 		$result = $this->client->get(uri: $url);

		if(str_contains(haystack: $result->getHeader('Content-Type')[0], needle: 'application/json') === false) {
			$url = rtrim(string: $url, characters: '/').'/apps/opencatalogi/api/directory';
			$result = $this->client->get(uri: $url);
		}

		$results = json_decode(json: $result->getBody()->getContents(), associative: true);

		$addedDirectories = [];
		$catalogs 		  = [];

		foreach($results['results'] as $record) {
			$catalogs[] = $record['catalogId'];
			$addedDirectories[] = $this->createDirectoryFromResult(result: $record, update: $update);

		}


		$localListings = $this->listingMapper->findAll(filters: ['directory' => $url]);

		foreach($localListings as $localListing) {
			if(in_array(needle: $localListing->getCatalogId(), haystack: $catalogs) === false) {
				$this->listingMapper->delete($localListing);
			}
		}

		return $addedDirectories;
	}

	public function updateToExternalDirectory(): array
	{
		return [];
	}

	public function listDirectory(array $filters = [], int $limit = 30, int $offset = 0): array
	{
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
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
		if($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$results = $this->listingMapper->findAll(filters: ['directory' => $directoryUrl, 'catalog_id' => $catalogId]);

			foreach($results as $result) {
				$this->listingMapper->delete(entity: $result);
			}

			return;
		}
		$dbConfig = [
			'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString(app: $this->appName, key: 'mongodbCluster')
		];

		$results = $this->objectService->findObjects(filters: ['directory' => $directoryUrl, 'catalogId' => $catalogId, '_schema' => 'directory'], config: $dbConfig);

		foreach($results['documents'] as $result) {
			$this->objectService->deleteObject(filters: ['_id' => $result['_id']], config: $dbConfig);
		}

		return;
	}

	public function directoryExists(string $catalogId, ?array &$listing = null): bool
	{
		$directoryUrl = $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"));

		if($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$results = $this->listingMapper->findAll(filters: ['directory' => $directoryUrl, 'catalog_id' => $catalogId]);

			$result = count($results) > 0;

			if($result === true) {
				$listing = $results[0]->jsonSerialize();
			}
			return $result;
		}
		$dbConfig = [
			'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString(app: $this->appName, key: 'mongodbCluster')
		];

		$results = $this->objectService->findObjects(filters: ['directory' => $directoryUrl, 'catalogId' => $catalogId, '_schema' => 'directory'], config: $dbConfig);


		$result =  count(value: $results['documents']) > 0;

		if($result === true) {
			$listing = $results['documents'][0];
		}

		return $result;
	}

	public function listCatalog (array $catalog): array
	{
		$existingListing = null;

		$catalogId = $catalog['id'];
		if($catalog['listed'] === false) {
			$this->deleteListing(catalogId: $catalogId, directoryUrl: $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index")),);
			return $catalog;
		}


		$listing = $this->getDirectoryEntry(catalogId: $catalogId);

		$listing['title']        = $catalog['title'];
		$listing['description']  = $catalog['description'];
		$listing['summary']      = $catalog['summary'];
		$listing['organisation'] = $catalog['organisation'];
		$listing['metadata']     = $catalog['metadata'];

		if($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			if($this->directoryExists(catalogId: $catalogId, listing: $existingListing) === true) {
				$listing = $this->listingMapper->updateFromArray(id: $existingListing['id'], object: $listing);
			} else {
				$listing = $this->listingMapper->createFromArray(object: $listing);
			}

			return $catalog;
		}

		try {
			$dbConfig = [
				'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
				'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
				'mongodbCluster' => $this->config->getValueString(app: $this->appName, key: 'mongodbCluster')
			];

			$listing['_schema'] = 'directory';

			if($this->directoryExists(catalogId: $catalogId, listing: $existingListing) === true) {
				$returnData = $this->objectService->updateObject(filters: ['id' => $existingListing['id']], update: $listing, config: $dbConfig);
			} else {
				$returnData = $this->objectService->saveObject(data: $listing, config: $dbConfig);
			}
			return $catalog;
		} catch (\Exception $e) {
			$catalog['listed'] = false;
			return $catalog;
		}

	}
}
