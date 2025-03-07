<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Exception\DirectoryUrlException;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Controller for handling Listing-related operations
 */
class ListingsController extends Controller
{
    /**
     * Constructor for ListingsController
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param ListingMapper $listingMapper The listing mapper
     * @param ObjectService $objectService The object service
     * @param DirectoryService $directoryService The directory service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly ListingMapper $listingMapper,
        private readonly ObjectService $objectService,
        private readonly DirectoryService $directoryService
    )
    {
        parent::__construct($appName, $request);
    }

	/**
	 * Retrieve a list of listings based on provided filters and parameters.
	 *
	 * @return JSONResponse JSON response containing the list of listings and total count
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch listing objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('listing', $requestParams);

        // Return JSON response
        return new JSONResponse($data);
    }

	/**
	 * Retrieve a specific listing by its ID.
	 *
	 * @param string|int $id The ID of the listing to retrieve
	 *
	 * @return JSONResponse JSON response containing the requested listing
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the listing object by its ID
        $object = $this->objectService->getObject('listing', $id);

        // Return the listing as a JSON response
        return new JSONResponse($object);
    }

	/**
	 * Create a new listing.
	 *
	 * @return JSONResponse The response containing the created listing object.
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function create(): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Remove the 'id' field if it exists, as we're creating a new object
        unset($data['id']);

        // Save the new listing object
        $object = $this->objectService->saveObject('listing', $data);

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

	/**
	 * Update an existing listing.
	 *
	 * @param string|int $id The ID of the listing to update.
	 *
	 * @return JSONResponse The response containing the updated listing object.
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function update(string|int $id): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Ensure the ID in the data matches the ID in the URL
        $data['id'] = $id;

        // Save the updated listing object
        $object = $this->objectService->saveObject('listing', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a listing.
     *
     * @param string|int $id The ID of the listing to delete.
	 *
     * @return JSONResponse The response indicating the result of the deletion.
	 * @throws ContainerExceptionInterface|NotFoundExceptionInterface|\OCP\DB\Exception
	 *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the listing object
        $result = $this->objectService->deleteObject('listing', $id);

        // Return the result as a JSON response
        return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }

	/**
	 * Synchronize a listing or all listings.
	 *
	 * @param string|null $id The ID of the listing to synchronize (optional).
	 *
	 * @return JSONResponse The response indicating the result of the synchronization.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function synchronise(?string $id = null): JSONResponse
    {
        // Synchronize the specified listing or all listings
        $result = $this->directoryService->synchronise($id);

        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }

	/**
	 * Add a new listing from a URL.
	 *
	 * @return JSONResponse The response indicating the result of adding the listing.
	 * @throws GuzzleException
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function add(): JSONResponse
    {
        // Get the URL parameter from the request
        $url = $this->request->getParam('url');

        // Add the new listing using the provided URL
		try {
			$result = $this->directoryService->syncExternalDirectory($url);
		} catch (DirectoryUrlException $exception) {
			if($exception->getMessage() === 'URL is required') {
				$exception->setMessage('Property "url" is required');
			}

			return new JSONResponse(data: ['message' => $exception->getMessage()], statusCode: 400);
		}

        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }
}
