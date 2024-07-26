<?php

namespace OCA\OpenCatalogi\Controller;

use Elastic\Elasticsearch\Client;
use GuzzleHttp\Exception\GuzzleException;
use OCA\opencatalogi\lib\Db\Publication;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use Symfony\Component\Uid\Uuid;

class PublicationsController extends Controller
{
    const TEST_ARRAY = [
        "354980e5-c967-4ba5-989b-65c2b0cd2ff4" => [
            "id" => "354980e5-c967-4ba5-989b-65c2b0cd2ff4",
            "name" => "Input voor OpenCatalogi",
            "summary" => "Dit is een selectie van high-value datasets in DCAT-AP 2.0 standaard x"
        ],
        "2ab0011e-9b4c-4c50-a50d-a16fc0be0178" => [
            "id" => "2ab0011e-9b4c-4c50-a50d-a16fc0be0178",
            "title" => "Publication two",
            "description" => "summary for two"
        ]
    ];

    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly PublicationMapper $publicationMapper,
		private readonly IAppConfig $config
	)
    {
        parent::__construct($appName, $request);
    }

	private function insertNestedObjects(array $object, ObjectService $objectService, array $config): array
	{
		//@TODO keep in mind that unpublished objects should not be inserted, and that objects should be updated if a subobject is updated.
		foreach($object as $key => $value) {
			try {
				if(
					is_string(value: $value)
					&& $key !== 'id'
					&& Uuid::isValid(uuid: $value) === true
					&& $subObject = $objectService->findObject(filters: ['_id' => $value], config: $config)
				) {
					$object[$key] = $subObject;
				}

				if(
					is_array(value: $value) === true
					&& array_is_list(array: $value) === true
				) {
					$object[$key] = $this->insertNestedObjects(object: $value, objectService: $objectService, config: $config);
				}
			} catch (GuzzleException $exception) {
				continue;
			}
		}

		return $object;
	}


	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(?string $getParameter)
    {
        // The TemplateResponse loads the 'main.php'
        // defined in our app's 'templates' folder.
        // We pass the $getParameter variable to the template
        // so that the value is accessible in the template.
        return new TemplateResponse(
            $this->appName,
            'PublicationsIndex',
            []
        );
    }

    /**
     * Taking it from a catalogue point of view is just adding a filter
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function catalog(string $id): TemplateResponse
    {
        // The TemplateResponse loads the 'main.php'
        // defined in our app's 'templates' folder.
        // We pass the $getParameter variable to the template
        // so that the value is accessible in the template.
        return new TemplateResponse(
            $this->appName,
            'PublicationsIndex',
            []
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse(['results'  => $this->publicationMapper->findAll()]);
		}

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters = $this->request->getParams();

		foreach($filters as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($filters[$key]);
			}
		}



		$filters['_schema'] = 'publication';

		$result = $objectService->findObjects(filters: $filters, config: $dbConfig);

        $results = ["results" => $result['documents']];
        return new JSONResponse($results);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string $id, ObjectService $objectService): JSONResponse
    {
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->publicationMapper->find($id));
		}

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = $id;

		$result = $objectService->findObject(filters: $filters, config: $dbConfig);

        return new JSONResponse($result);
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {
		$data = $this->request->getParams();

		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$returnData = $this->publicationMapper->createFromArray($data);
			$returnData = $returnData->jsonSerialize();
			$dbConfig = [];
		} else {
			$data['_schema'] = 'publication';

			$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');
			$returnData = $objectService->saveObject(
				data: $data,
				config: $dbConfig
			);
		}
		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticLocation') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticKey') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticIndex') !== ''
			&& $returnData['status'] === 'published'
		) {
			$elasticConfig['location'] = $this->config->getValueString(app: $this->appName, key: 'elasticLocation');
			$elasticConfig['key'] 	   = $this->config->getValueString(app: $this->appName, key: 'elasticKey');
			$elasticConfig['index']    = $this->config->getValueString(app: $this->appName, key: 'elasticIndex');

			$returnData = $this->insertNestedObjects($returnData, $objectService, $dbConfig);

			$returnData = $elasticSearchService->addObject(object: $returnData, config: $elasticConfig);

		}
        // get post from requests
        return new JSONResponse($returnData);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string $id, ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {

		$data = $this->request->getParams();

		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}
		if (isset($data['id'])) {
			unset( $data['id']);
		}

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$returnData = $this->publicationMapper->updateFromArray(id: $id, object: $data);
			$returnData = $returnData->jsonSerialize();

			$dbConfig = [];
		} else {
			$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

			$filters['_id'] = $id;
			$returnData = $objectService->updateObject(
				filters: $filters,
				update: $data,
				config: $dbConfig
			);
		}

		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticLocation') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticKey') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticIndex') !== ''
			&& $returnData['status'] === 'published'
		) {
			$elasticConfig['location'] = $this->config->getValueString(app: $this->appName, key: 'elasticLocation');
			$elasticConfig['key'] 	   = $this->config->getValueString(app: $this->appName, key: 'elasticKey');
			$elasticConfig['index']    = $this->config->getValueString(app: $this->appName, key: 'elasticIndex');

			$returnData = $this->insertNestedObjects($returnData, $objectService, $dbConfig);

			$returnData = $elasticSearchService->updateObject(id: $id, object: $returnData, config: $elasticConfig);

		}

		// get post from requests
		return new JSONResponse($returnData);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string $id, ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$this->publicationMapper->delete($this->publicationMapper->find($id));

			$returnData = [];
		} else {
			$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

			$filters['_id'] = $id;
			$returnData = $objectService->deleteObject(
				filters: $filters,
				config: $dbConfig
			);
		}

		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticLocation') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticKey') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticIndex') !== ''
			&& $returnData['status'] === 'published'
		) {
			$elasticConfig['location'] = $this->config->getValueString(app: $this->appName, key: 'elasticLocation');
			$elasticConfig['key'] 	   = $this->config->getValueString(app: $this->appName, key: 'elasticKey');
			$elasticConfig['index']    = $this->config->getValueString(app: $this->appName, key: 'elasticIndex');

			$returnData = $elasticSearchService->removeObject(id: $id, config: $elasticConfig);

		}

		// get post from requests
		return new JSONResponse($returnData);
    }
}
