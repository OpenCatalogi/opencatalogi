<?php

namespace OCA\OpenCatalogi\Controller;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\opencatalogi\lib\Db\Publication;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCA\OpenCatalogi\Service\DownloadService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ValidationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\IURLGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Uid\Uuid;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PublicationsController
 *
 * Controller for handling publication-related operations in the OpenCatalogi app.
 */
class PublicationsController extends Controller
{
    /**
     * PublicationsController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param PublicationMapper $publicationMapper The publication mapper
     * @param AttachmentMapper $attachmentMapper The attachment mapper
     * @param IAppConfig $config The app configuration
     * @param FileService $fileService The file service
     * @param DownloadService $downloadService The download service
     * @param ObjectService $objectService The object service
     * @param IURLGenerator $urlGenerator The URL generator
     *
     */
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly PublicationMapper $publicationMapper,
		private readonly AttachmentMapper $attachmentMapper,
		private readonly IAppConfig $config,
		private readonly FileService $fileService,
		private readonly DownloadService $downloadService,
		private readonly ObjectService $objectService,
		private readonly IURLGenerator $urlGenerator
	)
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of publications based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
	 *
     * @return JSONResponse JSON response containing the list of publications and total count
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch publication objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('publication', $requestParams);

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific publication by its ID.
     *
     * @param string|int $id The ID of the publication to retrieve
     * @param ObjectService $objectService Service to handle object operations
	 *
     * @return JSONResponse JSON response containing the requested publication
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
		$parameters = $this->request->getParams();

		$extend = [];

		if (isset($parameters['extend']) === true) {
			$extend = (array) $parameters['extend'];
		}

        // Fetch the publication object by its ID
        $object = $this->objectService->getObject(objectType: 'publication', id: $id, extend: $extend);

        // Return the publication as a JSON response
        return new JSONResponse($object);
    }

	/**
	 * Return all attachments for given publication.
	 *
	 * @param string|int $id The id of the publication.
	 *
	 * @return JSONResponse The Response containing attachments.
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function attachments(string|int $id): JSONResponse
	{
		// Fetch the publication object by its ID
		$object = $this->objectService->getObject('publication', $id);

		// Fetch attachment objects
		$objects = $this->objectService->getMultipleObjects(objectType: 'attachment', ids: $object['attachments']);

		// Prepare response data
		$data = [
			'results' => $objects,
			'total' => count($objects)
		];

		return new JSONResponse($data);
	}

	/**
	 * Download a publication in either PDF or ZIP format.
	 *
	 * This method handles the download request for a publication, supporting both PDF and ZIP formats.
	 * The format is determined by the 'Accept' header in the request.
	 *
	 * @param string|int $id The ID of the publication to download
	 * @param ObjectService $objectService The service to handle object operations
	 *
	 * @return JSONResponse The response containing either the file download or an error message
	 * @throws LoaderError|MpdfException|RuntimeError|SyntaxError
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function download(string|int $id, ObjectService $objectService): JSONResponse
	{
		// Determine the requested format based on the 'Accept' header
		return match ($this->request->getHeader('Accept')) {
			// If PDF is requested, create and return a PDF file
			'application/pdf' => $this->downloadService->createPublicationFile(objectService: $objectService, id: $id),
			// If ZIP is requested, create and return a ZIP file
			'application/zip' => $this->downloadService->createPublicationZip(objectService: $objectService, id: $id),
			// If an unsupported format is requested, return an error response
			default => new JSONResponse(
				data: ['error' => 'Unsupported Accept header, please use [application/pdf] or [application/zip]'],
				statusCode: 400
			),
		};
	}

    /**
     * Create a new publication.
     *
     * @param ObjectService $objectService The service to handle object operations
     * @return JSONResponse The response containing the created publication object
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
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

        // Save the new publication object
        $object = $this->objectService->saveObject('publication', $data);

        // If object is a class change it to array
        if (is_object($object) === true) {
            $object = $object->jsonSerialize();
        }

        // If we do not have an uri, we need to generate one
        if (isset($object['uri']) === false) {
            $object['uri'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('openCatalogi.publications.show', ['id' => $object['id']]));
            $object = $this->objectService->saveObject('publication', $object);
        }

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing publication.
     *
     * @param string|int $id The ID of the publication to update
     * @param ObjectService $objectService The service to handle object operations
	 *
     * @return JSONResponse The response containing the updated publication object
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
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
        $data['uri'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('openCatalogi.publications.show', ['id' => $data['id']]));

        // Save the updated publication object
        $object = $this->objectService->saveObject('publication', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a publication.
     *
     * @param string|int $id The ID of the publication to delete
     * @param ObjectService $objectService The service to handle object operations
	 *
     * @return JSONResponse The response indicating the result of the deletion
	 * @throws ContainerExceptionInterface|NotFoundExceptionInterface|\OCP\DB\Exception
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the publication object
        $result = $this->objectService->deleteObject('publication', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
}
