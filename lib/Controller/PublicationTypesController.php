<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\PublicationTypeMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\BroadcastService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\IURLGenerator;
/**
 * Class PublicationTypesController
 *
 * Controller for handling publication type-related operations in the OpenCatalogi app.
 */
class PublicationTypesController extends Controller
{
    /**
     * PublicationTypesController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param PublicationTypeMapper $publicationTypeMapper The publication type mapper
     * @param ObjectService $objectService The object service
     * @param DirectoryService $directoryService The directory service
     * @param BroadcastService $broadcastService The broadcast service
     * @param IURLGenerator $urlGenerator The URL generator
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly PublicationTypeMapper $publicationTypeMapper,
        private readonly ObjectService $objectService,
        private readonly DirectoryService $directoryService,
        private readonly BroadcastService $broadcastService,
        private readonly IURLGenerator $urlGenerator
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * Render the main page for publication types.
     *
     * @return TemplateResponse The rendered template response
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(): TemplateResponse
    {
        // Return a template response for the publication type index page
        return new TemplateResponse(
            $this->appName,
            'publicationTypeIndex',
            []
        );
    }

    /**
     * Retrieve a list of publication types based on provided filters and parameters.
     *
     * @return JSONResponse JSON response containing the list of publication types and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch publication type objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('publicationType', $requestParams);

        // Return JSON response with the fetched data
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific publication type by its ID.
     *
     * @param string|int $id The ID of the publication type to retrieve
     * @return JSONResponse JSON response containing the requested publication type
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the publication type object by its ID
        $object = $this->objectService->getObject('publicationType', $id);

        // Return the publication type as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new publication type.
     *
     * @return JSONResponse The response containing the created publication type object
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

        // Save the new publication type object
        $object = $this->objectService->saveObject('publicationType', $data);

        // If object is a class change it to array
        if (is_object($object) === true) {
            $object = $object->jsonSerialize();
        }

        // If we do not have an uri, we need to generate one
        if (isset($object['uri']) === false) {
            $object['uri'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('openCatalogi.publicationTypes.show', ['id' => $object['id']]));
            $object = $this->objectService->saveObject('publicationType', $object);
        }

		// Update all external directories
		$this->broadcastService->broadcast();

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing publication type.
     *
     * @param string|int $id The ID of the publication type to update
     * @return JSONResponse The response containing the updated publication type object
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

        // If we do not have an uri, we need to generate one
        $data['uri'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('openCatalogi.publicationTypes.show', ['id' => $data['id']]));


        // Save the updated publication type object
        $object = $this->objectService->saveObject('publicationType', $data);

		// Update all external directories
		$this->broadcastService->broadcast();

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a publication type.
     *
     * @param string|int $id The ID of the publication type to delete
     * @return JSONResponse The response indicating the result of the deletion
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the publication type object
        $result = $this->objectService->deleteObject('publicationType', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }

	/**
	 * Synchronize or delete a publication type based on listing status
	 *
	 * @PublicPage
	 * @NoCSRFRequired
	 * @return JSONResponse The JSON response containing the result of the operation
	 */
	public function synchronise(): JSONResponse
	{
		// Get the source and listed parameters from the request
		$source = $this->request->getParam('source');
		$listed = $this->request->getParam('listed', false);

		// Check if the source parameter is provided
		if (empty($source)) {
			return new JSONResponse(['error' => 'source parameter is required'], 400);
		}

		try {
			if ($listed) {
				// If listed is true, synchronize the publication type
				$syncedPublicationType = $this->directoryService->syncPublicationType($source);
				return new JSONResponse($syncedPublicationType);
			} else {
				// If listed is false, attempt to delete the publication type
				// Get all publication types
				$allPublicationTypes = $this->objectService->getObjects(
					objectType: 'publicationType',
				);

				// Filter publication types to only include those with a matching source
				$publicationTypes = array_filter($allPublicationTypes, function($publicationType) use ($source) {
					// Check if the publication type has a 'source' property and if it matches the given source
					return isset($publicationType['source']) && $publicationType['source'] === $source;
				});

				// Check the number of publication types found
				if (!empty($publicationTypes)) {
					$result = true;
					foreach ($publicationTypes as $publicationType) {
						$deleteResult = $this->objectService->deleteObject('publicationType', $publicationType['id']);
						$result = $result && $deleteResult;
					}
					return new JSONResponse(['success' => $result], $result ? 200 : 500);
				} else {
					// If no publication types are found, return an error
					return new JSONResponse(['error' => 'Publication type not found'], 404);
				}
			}
		} catch (\Exception $e) {
			// If an exception occurs, return an error response
			return new JSONResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
		}
	}
}
