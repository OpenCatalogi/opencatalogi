<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\SearchService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IUserManager;
use OCP\IUserSession;


/**
 * Class SearchController
 *
 * Controller for handling search-related operations in the OpenCatalogi app.
 */
class SearchController extends Controller
{
    /**
     * SearchController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param ObjectService $objectService The object service
     * @param PublicationMapper $publicationMapper The publication mapper
     * @param IAppConfig $config The app configuration
     * @param string $corsMethods Allowed CORS methods
     * @param string $corsAllowedHeaders Allowed CORS headers
     * @param int $corsMaxAge CORS max age
     */
    public function __construct(
        $appName,
        IRequest $request,
		private ObjectService $objectService,
		private readonly PublicationMapper $publicationMapper,
        private readonly IAppConfig $config,
        private readonly IUserManager $userManager,
        private readonly IUserSession $userSession,
		$corsMethods = 'PUT, POST, GET, DELETE, PATCH',
		$corsAllowedHeaders = 'Authorization, Content-Type, Accept',
		$corsMaxAge = 1728000
	) {
		parent::__construct($appName, $request);
		$this->corsMethods = $corsMethods;
		$this->corsAllowedHeaders = $corsAllowedHeaders;
		$this->corsMaxAge = $corsMaxAge;
    }

	/**
	 * Implements a preflighted CORS response for OPTIONS requests.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @since 7.0.0
	 *
	 * @return Response The CORS response
	 */
	#[NoCSRFRequired]
	#[PublicPage]
	public function preflightedCors(): Response {
		// Determine the origin
		$origin = isset($this->request->server['HTTP_ORIGIN']) ? $this->request->server['HTTP_ORIGIN'] : '*';

		// Create and configure the response
		$response = new Response();
		$response->addHeader('Access-Control-Allow-Origin', $origin);
		$response->addHeader('Access-Control-Allow-Methods', $this->corsMethods);
		$response->addHeader('Access-Control-Max-Age', (string)$this->corsMaxAge);
		$response->addHeader('Access-Control-Allow-Headers', $this->corsAllowedHeaders);
		$response->addHeader('Access-Control-Allow-Credentials', 'false');

		return $response;
	}

	/**
	 * Return all published publications.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing published publications.
	 * @throws GuzzleException
	 */
	public function index(): JSONResponse
	{			
		// Retrieve all request parameters
		$requestParams = $this->request->getParams();

		// Get publication objects based on request parameters
		$objects = $this->objectService->getResultArrayForRequest('publication', $requestParams);

		// Filter objects to only include published publications
		$filteredObjects = array_filter($objects['results'], function($object) {
			return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
		});

		// Prepare the response data
		$data = [
			'results' => array_values($filteredObjects), // Reset array keys
			'total' => count($filteredObjects)
		];

		return new JSONResponse($data);
	}

	/**
	 * Return all published publications.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing published publications.
	 * @throws GuzzleException
	 */
	public function publications(): JSONResponse
	{			
		// Retrieve all request parameters
		$requestParams = $this->request->getParams();
		$requestParams['status'] = 'Published';

		// Get publication objects based on request parameters
		$objects = $this->objectService->getResultArrayForRequest('publication', $requestParams);

		// Filter objects to only include published publications
//		$filteredObjects = array_filter($objects['results'], function($object) {
//			return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
//		});

		// Prepare the response data
		$data = [
			'results' => $objects['results'], // Reset array keys
			'facets' => $objects['facets'],
			'total' => $objects['total'],
			'page' => $objects['page'],
			'pages' => $objects['pages']
		];

		return new JSONResponse($data);
	}

	/**
	 * Return a specific publication by ID.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The ID of the publication.
	 *
	 * @return JSONResponse The Response containing the requested publication.
	 * @throws GuzzleException
	 */
	public function publication(string|int $publicationId): JSONResponse
	{
			
		$parameters = $this->request->getParams();

		$extend = [];

		if (isset($parameters['extend']) === true) {
			$extend = (array) $parameters['extend'];
		}

		try {
			// Fetch the publication object by its ID
			$object = $this->objectService->getObject(objectType: 'publication', id: $publicationId, extend: $extend);
			return new JSONResponse($object);
		} catch (Exception $e) {
			return new JSONResponse(
				['error' => 'Publication not found'],
				404
			);
		}
	}

	/**
	 * Return all attachments for a given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The ID of the publication.
	 *
	 * @return JSONResponse The Response containing the attachments.
	 * @throws GuzzleException
	 */
	public function attachments(string|int $publicationId): JSONResponse
	{		
		// Fetch the publication object by its ID
		$object = $this->objectService->getObject('publication', $publicationId);

		// Fetch attachment objects        
		$files = $this->objectService->getFiles('publication', $publicationId)['results'];

		// Clean up the files array
		$cleanedFiles = array_filter(array_map(function($file) {
			// Remove files without downloadUrl
			if (!isset($file['downloadUrl']) || empty($file['downloadUrl'])) {
				return null;
			}

			// Clean up labels if they exist
			if (isset($file['labels']) && is_array($file['labels'])) {
				$file['labels'] = array_filter(array_map(function($label) {
					// Remove entire label if it starts with 'object:'
					if (str_starts_with($label, 'object:')) {
						return null;
					}
					// Only remove 'woo_' prefix from remaining labels
					return preg_replace('/^woo_/', '', $label);
				}, $file['labels']));

				// Reindex labels array
				$file['labels'] = array_values($file['labels']);
			}

			return $file;
		}, $files));

		// Reindex array to ensure sequential keys
		$cleanedFiles = array_values($cleanedFiles);

		// Prepare response data
		$data = [
			'results' => $cleanedFiles,
			'total' => count($cleanedFiles),
			'page' => 1,
			'pages' => 1
		];

		return new JSONResponse($data);
	}

	/**
	 * Return all themes.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing all themes.
	 */
	public function themes(): JSONResponse
	{			
		// Get all attachment objects (Note: This might be a mistake, should probably be 'theme' instead of 'attachment')
		$objects = $this->objectService->getResultArrayForRequest(objectType: 'theme', requestParams: $this->request->getParams());

		// Prepare the response data
		$data = [
			'results' => $objects['results'], // Reset array keys
			'facets' => $objects['facets'],
			'total' => $objects['total'],
			'page' => $objects['page'],
			'pages' => $objects['pages']
		];

		return new JSONResponse($data);
	}

	/**
	 * Return a specific theme by ID.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $themeId The ID of the theme.
	 *
	 * @return JSONResponse The Response containing the requested theme.
	 * @throws GuzzleException
	 */
	public function theme(string|int $themeId): JSONResponse
	{			
		// Get the theme object by ID
		$object = $this->objectService->getObject('theme', $themeId);
		return new JSONResponse($object);
	}

	/**
	 * Return all pages.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing all pages.
	 */
	public function pages(): JSONResponse
	{			
		// Get all page objects with request parameters
		$objects = $this->objectService->getResultArrayForRequest(objectType: 'page', requestParams: $this->request->getParams());

		// Format dates for each result
		$formattedResults = array_map(function($object) {
			// Format created_at if it exists
			if (isset($object['created_at'])) {
				$created = new \DateTime($object['created_at']);
				$object['created_at'] = $created->format('Y-m-d\TH:i:s.u\Z');
			}
			// Format updated_at if it exists 
			if (isset($object['updated_at'])) {
				$updated = new \DateTime($object['updated_at']);
				$object['updated_at'] = $updated->format('Y-m-d\TH:i:s.u\Z'); 
			}
			return $object;
		}, $objects['results']);

		// Prepare the response data with formatted dates
		$data = [
			'data' => $formattedResults
		];

		return new JSONResponse($data);
	}

	/**
	 * Return a specific page by slug.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $pageSlug The slug of the page
	 * @return JSONResponse The Response containing the requested page
	 * @throws GuzzleException
	 */
	public function page(string $pageSlug): JSONResponse 
	{			
		// Get the page object by slug
		$object = $this->objectService->getObject('page', $pageSlug);
		
		// Format the date fields to match required format
		if (isset($object['created_at'])) {
			$created = new \DateTime($object['created_at']);
			$object['created_at'] = $created->format('Y-m-d\TH:i:s.u\Z');
		}
		if (isset($object['updated_at'])) {
			$updated = new \DateTime($object['updated_at']); 
			$object['updated_at'] = $updated->format('Y-m-d\TH:i:s.u\Z');
		}
		
		return new JSONResponse($object);
	}

	/**
	 * Return all menus.
	 *	
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing all menus.
	 */
	public function menu(): JSONResponse
	{			
		// Get all page objects with request parameters
		$objects = $this->objectService->getResultArrayForRequest(objectType: 'menu', requestParams: $this->request->getParams());

		// Format dates for each result
		$formattedResults = array_map(function($object) {
			// Format created_at if it exists
			if (isset($object['created_at'])) {
				$created = new \DateTime($object['created_at']);
				$object['created_at'] = $created->format('Y-m-d\TH:i:s.u\Z');
			}
			// Format updated_at if it exists 
			if (isset($object['updated_at'])) {
				$updated = new \DateTime($object['updated_at']);
				$object['updated_at'] = $updated->format('Y-m-d\TH:i:s.u\Z'); 
			}
			return $object;
		}, $objects['results']);

		// Prepare the response data with formatted dates
		$data = [
			'data' => $formattedResults
		];

		return new JSONResponse($data);
	}
}
