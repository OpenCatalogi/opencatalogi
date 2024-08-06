<?php

namespace OCA\OpenCatalogi\Controller;

// index(): the index method to pass parameters as an associative array instead of named parameters. 


use OCA\OpenCatalogi\Db\MetaDataMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class MetaDataController extends Controller
{

    public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly MetaDataMapper $metaDataMapper
	)
    {
        parent::__construct($appName, $request);
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
            'metaDataIndex',
            []
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function index(ObjectService $objectService): JSONResponse
	{
        $filters = $this->request->getParams();

        $searchConditions = [];
        $searchParams = [];
        $fieldsToSearch = ['title', 'description'];

        foreach ($filters as $key => $value) {
            if ($key === '_search') {
                // MongoDB
                $searchRegex = ['$regex' => $value, '$options' => 'i'];
                $filters['$or'] = [];

                // MySQL
                $searchParams['search'] = '%' . strtolower($value) . '%';

                foreach ($fieldsToSearch as $field) {
                    // MongoDB
                    $filters['$or'][] = [$field => $searchRegex];

                    // MySQL
                    $searchConditions[] = "LOWER($field) LIKE :search";
                }
            }

            if (str_starts_with($key, '_')) {
                unset($filters[$key]);
            } 
        }

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
            // Unset mongodb filter
            unset($filters['$or']);

			return new JSONResponse(['results' =>$this->metaDataMapper->findAll(filters: $filters, searchParams: $searchParams, searchConditions: $searchConditions)]);
		}

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_schema'] = 'metadata';

		$result = $objectService->findObjects(filters: $filters, config: $dbConfig);

		$results = ["results" => $result['documents']];
		return new JSONResponse($results);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(string|int $id, ObjectService $objectService): JSONResponse
	{
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->metaDataMapper->find(id: (int) $id));
		}
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = (string) $id;

		$result = $objectService->findObject(filters: $filters, config: $dbConfig);

		return new JSONResponse($result);
	}


	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function create(ObjectService $objectService): JSONResponse
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
			return new JSONResponse($this->metaDataMapper->createFromArray(object: $data));
		}
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$data['_schema'] = 'metadata';

		$returnData = $objectService->saveObject(
			data: $data,
			config: $dbConfig
		);

		// get post from requests
		return new JSONResponse($returnData);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function update(string|int $id, ObjectService $objectService): JSONResponse
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
			return new JSONResponse($this->metaDataMapper->updateFromArray(id: (int) $id, object: $data));
		}


		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = (string) $id;
		$returnData = $objectService->updateObject(
			filters: $filters,
			update: $data,
			config: $dbConfig
		);

		// get post from requests
		return new JSONResponse($returnData);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function destroy(string|int $id, ObjectService $objectService): JSONResponse
	{
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$this->metaDataMapper->delete($this->metaDataMapper->find(id: (int) $id));

			return new JSONResponse([]);
		}
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = (string) $id;
		$returnData = $objectService->deleteObject(
			filters: $filters,
			config: $dbConfig
		);

		// get post from requests
		return new JSONResponse($returnData);
	}
}