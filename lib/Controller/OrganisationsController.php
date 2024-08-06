<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\OrganisationMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class OrganisationsController extends Controller
{
    public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly OrganisationMapper $organisationMapper
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
        return new TemplateResponse($this->appName, 'OrganisationIndex', []);
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

			return new JSONResponse(['results' => $this->organisationMapper->findAll(filters: $filters, searchParams: $searchParams, searchConditions: $searchConditions)]);
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
	 * Read a single object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function show(string $id, ObjectService $objectService): JSONResponse
	{
		// Latere zorg
		$query= $this->request->getParams();

		$results = $callService->show(source: 'brc', endpoint: 'besluiten', id: $id);
		return new JSONResponse($results);
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
			return new JSONResponse($this->organisationMapper->createFromArray(object: $data));
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
		$body = $this->request->getParams();
		$results = $callService->update(source: 'brc', endpoint: 'besluiten', data: $body, id: $id);
		return new JSONResponse($results);
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
		$callService->destroy(source: 'brc', endpoint: 'besluiten', id: $id);

		return new JsonResponse([]);
	}
}
