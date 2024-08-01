<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class CatalogiController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
		private readonly CatalogMapper $catalogMapper
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(?string $getParameter): TemplateResponse
    {
        return new TemplateResponse($this->appName, 'CatalogiIndex', []);
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

			return new JSONResponse(['results' => $this->catalogMapper->findAll(filters: $filters, searchParams: $searchParams, searchConditions: $searchConditions)]);
		}
        
        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_schema'] = 'catalog';

            $result = $objectService->findObjects(filters: $filters, config: $dbConfig);

            return new JSONResponse(["results" => $result['documents']]);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
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
			return new JSONResponse($this->catalogMapper->find(id: (int) $id));
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = $id;

            $result = $objectService->findObject($filters, $dbConfig);

            return new JSONResponse($result);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
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
			return new JSONResponse($this->catalogMapper->createFromArray(object: $data));
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $data['_schema'] = 'catalog';

            $returnData = $objectService->saveObject($data, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
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
			return new JSONResponse($this->catalogMapper->updateFromArray(id: (int) $id, object: $data));
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = $id;
            $returnData = $objectService->updateObject($filters, $data, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string $id, ObjectService $objectService): JSONResponse
    {
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$this->catalogMapper->delete($this->catalogMapper->find((int) $id));

			return new JSONResponse([]);
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = $id;
            $returnData = $objectService->deleteObject($filters, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }
}
