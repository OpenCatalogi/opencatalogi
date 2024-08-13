<?php

namespace OCA\OpenCatalogi\Service;

use OCA\OpenCatalogi\Db\CatalogMapper;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\IAppConfig;

class ValidationService
{
	/**
	 * @var string The name of the application.
	 */
	private string $appName;

	/**
	 * @var array The current MongoDB Config.
	 */
	private array $mongodbConfig;

	/**
	 * @param IAppConfig    $config		   The application config
	 * @param CatalogMapper $catalogMapper The catalog mapper.
	 * @param ObjectService $objectService The object service.
	 */
	public function __construct(
		private readonly IAppConfig    $config,
		private readonly CatalogMapper $catalogMapper,
		private readonly ObjectService $objectService,
	){
		$this->appName = 'opencatalogi';

		$this->mongodbConfig = [
			'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString(app: $this->appName, key:'mongodbCluster')
		];

	}

	/**
	 * @return array The mongodb config.
	 */
	public function getMongodbConfig(): array
	{
		return $this->mongodbConfig;
	}

	/**
	 * Fetches a catalog from either the local database or mongodb
	 *
	 * @param  string $id The id of the catalog to be fetched.
	 * @return array      The JSON Serialised catalog.
	 *
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getCatalog (string $id): array
	{
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$filter = ['id' => $id, '_schema' => 'catalog'];

			return $this->objectService->findObject(filters: $filter, config: $this->getMongodbConfig());
		}

		return $this->catalogMapper->find(id: $id)->jsonSerialize();
	}

	/**
	 * Validates a publication against the rules set for the publication.
	 *
	 * @param  array $publication The publication to be validated.
	 * @return array 			  The publication after it has been validated.
	 *
	 * @throws OCSBadRequestException Thrown if the object does not validate
	 */
	public function validatePublication(array $publication): array
	{
		$catalogId  = $publication['catalog'];
		$metadata   = $publication['metadata'];

		$catalog = $this->getCatalog($catalogId);

		if(in_array(needle: $metadata, haystack: $catalog['metadata']) === false) {
			throw new OCSBadRequestException(message: 'Given metadata object not present in catalog');
		}

		return $publication;
	}

}
