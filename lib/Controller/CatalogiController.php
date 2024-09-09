<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
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
    public function index(ObjectService $objectService, SearchService $searchService): JSONResponse
    {
        $filters = $this->request->getParams();
		unset($filters['_route']);
        $fieldsToSearch = ['title', 'description', 'summary'];

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$searchParams = $searchService->createMySQLSearchParams(filters: $filters);
			$searchConditions = $searchService->createMySQLSearchConditions(filters: $filters, fieldsToSearch:  $fieldsToSearch);
			$filters = $searchService->unsetSpecialQueryParams(filters: $filters);

			return new JSONResponse(['results' => $this->catalogMapper->findAll(limit: null, offset: null, filters: $filters, searchConditions: $searchConditions, searchParams: $searchParams)]);
		}

		$filters = $searchService->createMongoDBSearchFilter(filters: $filters, fieldsToSearch: $fieldsToSearch);
		$filters = $searchService->unsetSpecialQueryParams(filters: $filters);

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
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			try {
				return new JSONResponse($this->catalogMapper->find(id: (int) $id));
			} catch (DoesNotExistException $exception) {
				return new JSONResponse(data: ['error' => 'Not Found'], statusCode: 404);
			}
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
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(ObjectService $objectService, DirectoryService $directoryService): JSONResponse
    {
		$data = $this->request->getParams();

		// Remove fields we should never post
		unset($data['id']);
		foreach ($data as $key => $value) {
			if (str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}

        // @todo dit is alleen omdat er een onredenlijke verplichting aan de database is toegeveoegd
        if(array_key_exists('metadata',$data) === false){
            $data['metadata'] = [];
        }

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$result = $this->catalogMapper->createFromArray(object: $data);

			$resultArray = $directoryService->listCatalog($result->jsonSerialize());

			$result->hydrate($resultArray);
			$this->catalogMapper->update($result);

			return new JSONResponse($result);
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $data['_schema'] = 'catalog';

            $returnData  = $objectService->saveObject($data, $dbConfig);
			$resultArray = $directoryService->listCatalog($returnData);
			$returnData  = $objectService->updateObject(['_id' => $resultArray['_id']], $resultArray, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string|int $id, ObjectService $objectService, DirectoryService $directoryService): JSONResponse
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
			$result = $this->catalogMapper->updateFromArray(id: (int) $id, object: $data);

			$resultArray = $directoryService->listCatalog($result->jsonSerialize());

			$result->hydrate($resultArray);
			$this->catalogMapper->update($result);

			return new JSONResponse($result);
		}

        try {
            $dbConfig = [
                'base_uri' => $this->config->getValueString($this->appName, 'mongodbLocation'),
                'headers' => ['api-key' => $this->config->getValueString($this->appName, 'mongodbKey')],
                'mongodbCluster' => $this->config->getValueString($this->appName, 'mongodbCluster')
            ];

            $filters['_id'] = (string) $id;
            $returnData  = $objectService->updateObject($filters, $data, $dbConfig);
			$resultArray = $directoryService->listCatalog($returnData);
			$returnData  = $objectService->updateObject($filters, $resultArray, $dbConfig);


			return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService, DirectoryService $directoryService): JSONResponse
    {
		$directoryService->listCatalog(['id' => $id, 'listed' => false]);


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

            $filters['_id'] = (string) $id;
            $returnData = $objectService->deleteObject($filters, $dbConfig);

            return new JSONResponse($returnData);
        } catch (\Exception $e) {
            return new JSONResponse(['error' => $e->getMessage()], 500);
        }
    }
}
