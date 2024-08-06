<?php

namespace OCA\OpenCatalogi\Controller;

use Elastic\Elasticsearch\Client;
use GuzzleHttp\Exception\GuzzleException;
use OCA\opencatalogi\lib\Db\Theme;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use Symfony\Component\Uid\Uuid;

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
		private readonly PublicationMapper $publicationMapper,
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
        return new TemplateResponse(
            //Application::APP_ID,
            'zaakafhandelapp',
            'index',
            []
        );
	}

	/**
	 * Return (and serach) all objects
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function index(CallService $callService): JSONResponse
	{
		// Latere zorg
		$query= $this->request->getParams();

		$results = $callService->index(source: 'brc', endpoint: 'besluiten');
		return new JSONResponse($results);
	}

	/**
	 * Read a single object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function show(string $id, CallService $callService): JSONResponse
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
	public function create(CallService $callService): JSONResponse
	{
		// get post from requests
		$body = $this->request->getParams();
		$results = $callService->create(source: 'brc', endpoint: 'besluiten', data: $body);
		return new JSONResponse($results);
	}

	/**
	 * Update an object
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse
	 */
	public function update(string $id, CallService $callService): JSONResponse
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
	public function destroy(string $id, CallService $callService): JSONResponse
	{
		$callService->destroy(source: 'brc', endpoint: 'besluiten', id: $id);

		return new JsonResponse([]);
	}
}
