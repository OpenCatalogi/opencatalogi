<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\ThemeMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class ThemesController extends Controller
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
		private readonly ThemeMapper $themeMapper,
		private readonly IAppConfig $config,
	)
    {
        parent::__construct($appName, $request);
    }

	/**
	 * This returns the template of the main app's page
	 * It adds some data to the template (app version)
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function page(): TemplateResponse
	{
        return new TemplateResponse($this->appName, 'ThemesIndex', []);
	}

	/**
	 * Return (and serach) all objects
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function index(ObjectService $objectService): JSONResponse
	{
		
        $filters = $this->request->getParams();

        $searchConditions = [];
        $searchParams = [];
        $fieldsToSearch = ['title', 'description', 'summary'];

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

			return new JSONResponse(['results' => $this->themeMapper->findAll(filters: $filters, searchParams: $searchParams, searchConditions: $searchConditions)]);
		}
        
        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_schema'] = 'theme';

            $result = $objectService->findObjects(filters: $filters, config: $dbConfig);

            return new JSONResponse(["results" => $result['documents']]);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
	}

	/**
	 * Read a single object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function show(string $id, ObjectService $objectService): JSONResponse
	{
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->themeMapper->find(id: (int) $id));
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = (string) $id;

            $result = $objectService->findObject($filters, $dbConfig);

            return new JSONResponse($result);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
	}


	/**
	 * Creatue an object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function create(ObjectService $objectService): JSONResponse
	{
		
		$data = $this->request->getParams();

		foreach ($data as $key => $value) {
			if (str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->themeMapper->createFromArray(object: $data));
		}

		try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_schema'] = 'organisation';

            $result = $objectService->findObjects(filters: $filters, config: $dbConfig);

            return new JSONResponse(["results" => $result['documents']]);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
	}

	/**
	 * Update an object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function update(string $id, ObjectService $objectService): JSONResponse
	{
		$data = $this->request->getParams();

		foreach ($data as $key => $value) {
			if (str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}
		if (isset($data['id'])) {
			unset($data['id']);
		}

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->themeMapper->updateFromArray(id: (int) $id, object: $data));
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = (string) $id;
            $returnData = $objectService->updateObject($filters, $data, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
	}

	/**
	 * Delate an object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function destroy(string $id, ObjectService $objectService): JSONResponse
	{
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$this->themeMapper->delete($this->themeMapper->find((int) $id));

			return new JSONResponse([]);
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = (string) $id;
            $returnData = $objectService->deleteObject($filters, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }
}
