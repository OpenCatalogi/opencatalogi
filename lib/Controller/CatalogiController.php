<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\BroadcastService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\IURLGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class CatalogiController
 *
 * Controller for handling catalog-related operations in the OpenCatalogi app.
 */
class CatalogiController extends Controller
{
    /**
     * CatalogiController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param CatalogMapper $catalogMapper The catalog mapper
     * @param ObjectService $objectService The object service
	 * @param DirectoryService $directoryService The directory service
	 * @param BroadcastService $broadcastService The broadcast service
     * @param IURLGenerator $urlGenerator The URL generator
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly CatalogMapper $catalogMapper,
        private readonly ObjectService $objectService,
		private readonly DirectoryService $directoryService,
		private readonly BroadcastService $broadcastService,
        private readonly IURLGenerator $urlGenerator
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of catalogs based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
	 *
     * @return JSONResponse JSON response containing the list of catalogs and total count
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch catalog objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('catalog', $requestParams);

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific catalog by its ID.
     *
     * @param string|int $id The ID of the catalog to retrieve
     * @param ObjectService $objectService Service to handle object operations
	 *
     * @return JSONResponse JSON response containing the requested catalog
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the catalog object by its ID
        $object = $this->objectService->getObject('catalog', $id);

        // Return the catalog as a JSON response
        return new JSONResponse($object);
    }

	/**
	 * Create a new catalog.
	 *
	 * @param ObjectService $objectService The service to handle object operations.
	 *
	 * @return JSONResponse The response containing the created catalog object.
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 * @throws GuzzleException
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function create(ObjectService $objectService): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Remove the 'id' field if it exists, as we're creating a new object
        unset($data['id']);

        // Save the new catalog object
        $object = $this->objectService->saveObject('catalog', $data);

        // If object is a class change it to array
        if (is_object($object) === true) {
            $object = $object->jsonSerialize();
        }

        // If we do not have an uri, we need to generate one
        if (isset($object['uri']) === false) {
            $object['uri'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('openCatalogi.catalogs.show', ['id' => $object['id']]));
            $object = $this->objectService->saveObject('catalog', $object);
        }

		// Update all external directories
		$this->broadcastService->broadcast();

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

	/**
	 * Update an existing catalog.
	 *
	 * @param string|int $id The ID of the catalog to update.
	 * @param ObjectService $objectService The service to handle object operations.
	 *
	 * @return JSONResponse The response containing the updated catalog object.
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 * @throws GuzzleException
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function update(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Ensure the ID in the data matches the ID in the URL
        $data['id'] = $id;

        // If we do not have an uri, we need to generate one
        $data['uri'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('openCatalogi.catalogs.show', ['id' => $data['id']]));

        // Save the updated catalog object
        $object = $this->objectService->saveObject('catalog', $data);

		// Update all external directories
		$this->broadcastService->broadcast();

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a catalog.
     *
     * @param string|int $id The ID of the catalog to delete.
     * @param ObjectService $objectService The service to handle object operations.
	 *
     * @return JSONResponse The response indicating the result of the deletion.
	 * @throws ContainerExceptionInterface|NotFoundExceptionInterface|\OCP\DB\Exception
	 *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the catalog object
        $result = $this->objectService->deleteObject('catalog', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
}
