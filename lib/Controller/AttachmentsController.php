<?php

namespace OCA\OpenCatalogi\Controller;

ini_set('memory_limit', '2048M');

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\IURLGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Uid\Uuid;

/**
 * Class AttachmentsController
 *
 * This controller handles CRUD operations for attachments in the OpenCatalogi app.
 */
class AttachmentsController extends Controller
{
    /**
     * AttachmentsController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param AttachmentMapper $attachmentMapper The attachment mapper
     * @param FileService $fileService The file service
     * @param IUserSession $userSession The user session
     * @param ObjectService $objectService The object service
     * @param IURLGenerator $urlGenerator The URL generator
     */
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly AttachmentMapper $attachmentMapper,
		private readonly FileService $fileService,
		private readonly IUserSession $userSession,
		private readonly ObjectService $objectService,
		private readonly IURLGenerator $urlGenerator
	)
    {
        parent::__construct($appName, $request);
    }

	/**
	 * Create a new attachment.
	 *
	 * @param ObjectService $objectService The service to handle object operations.
	 *
	 * @return JSONResponse The response containing the created attachment object.
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
	 * @throws Exception
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

        // Check if we have a publication ID
        if (empty($data['publication'])) {
            return new JSONResponse(['error' => 'Publication is required (should be an id)'], 400);
        }

        // Check if we have either downloadUrl or _file content
        if (empty($data['downloadUrl']) && empty($data['_file'])) {
            return new JSONResponse(['error' => 'Either downloadUrl or file content is required'], 400);
        }

        // Handle file upload - either from downloadUrl or _file content
        if (!empty($data['downloadUrl'])) {
            // File is provided via URL
            $fileData = [
                'title' => $data['title'] ?? basename($data['downloadUrl']),
                'content' => file_get_contents($data['downloadUrl'])
            ];
        } else {
            // File is provided as binary/base64 content
            $fileData = [
                'title' => $data['title'] ?? 'Untitled',
                'content' => is_string($data['_file']) ? $data['_file'] : base64_encode($data['_file']) // Convert binary content to base64 if needed
            ];
        }
      
        // Handle labels/tags
        if (!empty($data['labels'])) {
            $data['tags'] = $data['labels']; // Copy labels to tags for backwards compatibility
        }
        // Convert legacy fields to tags for backwards compatibility
        $data['tags'] = $data['tags'] ?? [];
        
        // Map legacy fields to tags in property:value format
        $legacyFields = ['reference', 'summary', 'description', 'published', 'license'];
        foreach ($legacyFields as $field) {
            if (!empty($data[$field])) {
                $data['tags'][] = $field . ':' . $data[$field];
            }
        }

        // Get the publication object that this attachment belongs to
        $publication = $objectService->getObject('publication', $data['publication']);
         // Create the file on the publication object and return the result directly
         $object = $objectService->createFile('publication', $publication['id'], [
             'filePath' => $fileData['title'],
             'content' => $fileData['content'], 
             'tags' => $data['tags'] ?? []
         ]);

        return new JSONResponse($object);
    }
}
