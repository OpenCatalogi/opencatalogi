<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalog\Service\ElasticSearchService;
use OCA\OpenCatalog\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use Symfony\Component\Uid\Uuid;

class AttachmentsController extends Controller
{

    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config
	)
    {
        parent::__construct($appName, $request);
    }

	private function insertNestedObjects(array $object, ObjectService $objectService, array $config): array
	{
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
            //Application::APP_ID,
            $this->appName,
            'AttachmentsIndex',
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
            //Application::APP_ID,
            $this->appName,
            'AttachmentsIndex',
            []
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters = $this->request->getParams();

		foreach($filters as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($filters[$key]);
			}
		}



		$filters['_schema'] = 'attachment';

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
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$data = $this->request->getParams();

		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}

		$data['_schema'] = 'attachment';

		$returnData = $objectService->saveObject(
			data: $data,
			config: $dbConfig
		);


		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
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
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$data = $this->request->getParams();

		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}
		if (isset($data['id'])) {
			unset( $data['id']);
		}

		$filters['_id'] = $id;
		$returnData = $objectService->updateObject(
			filters: $filters,
			update: $data,
			config: $dbConfig
		);

		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
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
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = $id;
		$returnData = $objectService->deleteObject(
			filters: $filters,
			config: $dbConfig
		);

		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
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
