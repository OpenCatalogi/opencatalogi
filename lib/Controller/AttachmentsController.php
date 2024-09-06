<?php

namespace OCA\OpenCatalogi\Controller;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\IUserSession;
use Symfony\Component\Uid\Uuid;

class AttachmentsController extends Controller
{

    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly AttachmentMapper $attachmentMapper,
		private readonly FileService $fileService,
		private readonly IUserSession $userSession,
	)
    {
        parent::__construct($appName, $request);
    }

	private function insertNestedObjects(array $object, ObjectService $objectService, array $config): array
	{
		foreach($object as $key => $value) {
			try {
				if(
					is_string(value: $value)
					&& $key !== 'id'
					&& Uuid::isValid(uuid: $value) === true
					&& $subObject = $objectService->findObject(filters: ['_id' => $value], config: $config)
				) {
					$object[$key] = $subObject;
				}
			} catch (GuzzleException $exception) {
				continue;
			}
		}

		return $object;
	}

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(?string $getParameter)
    {
        // The TemplateResponse loads the 'main.php'
        // defined in our app's 'templates' folder.
        // We pass the $getParameter variable to the template
        // so that the value is accessible in the template.
        return new TemplateResponse(
            //Application::APP_ID,
            $this->appName,
            'AttachmentsIndex',
            []
        );
    }

    /**
     * Taking it from a catalogue point of view is just adding a filter
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function catalog(string|int $id): TemplateResponse
    {
        // The TemplateResponse loads the 'main.php'
        // defined in our app's 'templates' folder.
        // We pass the $getParameter variable to the template
        // so that the value is accessible in the template.
        return new TemplateResponse(
            //Application::APP_ID,
            $this->appName,
            'AttachmentsIndex',
            []
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			return new JSONResponse(['results' => $this->attachmentMapper->findAll()]);
		}
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters = $this->request->getParams();
		unset($filters['_route']);

		foreach($filters as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($filters[$key]);
			}
		}

		$filters['_schema'] = 'attachment';

		$result = $objectService->findObjects(filters: $filters, config: $dbConfig);

        $results = ["results" => $result['documents']];
        return new JSONResponse($results);
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			try {
				return new JSONResponse($this->attachmentMapper->find(id: (int) $id));
			} catch (DoesNotExistException $exception) {
				return new JSONResponse(data: ['error' => 'Not Found'], statusCode: 404);
			}
		}
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = (string) $id;

		$result = $objectService->findObject(filters: $filters, config: $dbConfig);

        return new JSONResponse($result);
    }


	/**
	 * Gets info about the uploaded file from the request body, looks specifically for the field '_file'.
	 * If there is no file or there is an error loading it this will return an error response.
	 *
	 * @return JSONResponse|array An error response or an array containing the info about the uploaded file.
	 */
	private function checkUploadedFile(): JSONResponse|array
	{
		$uploadedFile = $this->request->getUploadedFile(key: '_file');

		if (empty($uploadedFile) === true) {
			return new JSONResponse(data: ['error' => 'Please upload a file using key "_file" or give a "downloadUrl"'], statusCode: 400);
		}

		// Check for upload errors.
		if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
			return new JSONResponse(data: ['error' => 'File upload error: '.$uploadedFile['error']], statusCode: 400);
		}

		return $uploadedFile;
	}

	/**
	 * Validates if the URL fields are actual valid urls (or null).
	 *
	 * @param array $data The form-data fields and their values (/request body).
	 *
	 * @return JSONResponse|array An error response if there are validation errors or an array containing all request body params.
	 */
	private function checkRequestBody(array $data): JSONResponse|array
	{
		$errorMsg = [];
		if (empty($data['accessUrl']) === false && filter_var(value: $data['accessUrl'], filter: FILTER_VALIDATE_URL) === false) {
			$errorMsg[] = "accessUrl is not a valid url";
		}

		if (empty($data['downloadUrl']) === false && filter_var(value: $data['downloadUrl'], filter: FILTER_VALIDATE_URL) === false) {
			$errorMsg[] = "downloadUrl is not a valid url";
		}

		if (empty($errorMsg) === false) {
			return new JSONResponse(data: ['validation_errors' => $errorMsg], statusCode: 400);
		}

		return $data;
	}

	/**
	 * If it does not already exist creates a folder for the publication the new Attachment belongs to in NextCloud,
	 * so that the uploaded file(s) for that publication can be saved there. After that saves the uploaded file in that folder.
	 * If the file is created without error this will return the full path to the file from the root/user folder.
	 *
	 * @param array $uploadedFile Information about the uploaded file from the request body.
	 *
	 * @return JSONResponse|string An error response if creating the file in NextCloud failed or a string path to the created file.
	 * @throws Exception In case creating a folder or new file fails.
	 */
	private function handleFile(array $uploadedFile): JSONResponse|string
	{
		// Create the Publicaties folder, the Publication specific folder and the Bijlagen folder in that.
		$this->fileService->createFolder(folderPath: 'Publicaties');
		$publicationFolder = $this->fileService->getPublicationFolderName(
			publicationId: $this->request->getHeader('Publication-Id'),
			publicationTitle: $this->request->getHeader('Publication-Title')
		);
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder/Bijlagen");

		// Save the uploaded file.
		$filePath = "Publicaties/$publicationFolder/Bijlagen/" . $uploadedFile['name']; // Add a file version to the file name?
		$created = $this->fileService->uploadFile(
			content: file_get_contents(filename: $uploadedFile['tmp_name']),
			filePath: $filePath
		);

		if ($created === false) {
			return new JSONResponse(data: ['error' => "Failed to upload file. This file: $filePath might already exist"], statusCode: 400);
		}

		return $filePath;
	}


	/**
	 * Adds information about the uploaded file to the appropriate Attachment fields. Inclusive share link.
	 *
	 * @param array $data The form-data fields and their values (/request body) that we are going to update before posting the Attachment.
	 * @param array $uploadedFile Information about the uploaded file from the request body.
	 * @param string $filePath The full file path to where the file is stored in NextCloud.
	 *
	 * @return array The updated $data array
	 * @throws Exception In case creating the share(link) fails.
	 */
	private function AddFileInfoToData(array $data, array $uploadedFile, string $filePath): array
	{
		// Update Attachment data.
		$currentUser = $this->userSession->getUser();
		$userId = $currentUser ? $currentUser->getUID() : 'Guest';
		$data['reference'] = "$userId/$filePath";
		$data['type'] = $uploadedFile['type'];
		$data['size'] = $uploadedFile['size'];
		$explodedName = explode(separator: '.', string: $uploadedFile['name']);
		$data['title'] = $explodedName[0];
		$data['extension'] = end(array: $explodedName);

		// Create ShareLink.
		$shareLink = $this->fileService->createShareLink(path: $filePath);
		if (empty($data['accessUrl']) === true) {
			$data['accessUrl'] = $shareLink;
		}
		if (empty($data['downloadUrl']) === true) {
			$data['downloadUrl'] =  "$shareLink/download";
		}

		return $data;
	}


	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @throws Exception In case creating a new folder, the file upload to NextCloud, or creating the share link fails.
	 * @throws GuzzleException In case saving the Attachment to MongoDB fails.
	 */
    public function create(ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {
		$data = $this->request->getParams();
		// Uploaded _file and downloadURL are mutually exclusive.
		if (empty($data['downloadUrl']) === true) {
			// Check if a file was uploaded.
			$uploadedFile = $this->checkUploadedFile();
			if ($uploadedFile instanceof JSONResponse) {
				return $uploadedFile;
			}
		}

		// Get form-data field/request body and validate the input.
		$data = $this->checkRequestBody($data);
		if ($data instanceof JSONResponse) {
			return $data;
		}

		if (empty($uploadedFile) === false) {
			// Handle saving the uploaded file in NextCloud.
			$filePath = $this->handleFile(uploadedFile: $uploadedFile);
			if ($filePath instanceof JSONResponse) {
				return $filePath;
			}

			// Update Attachment data, inclusive share link.
			$data = $this->AddFileInfoToData(data: $data, uploadedFile: $uploadedFile, filePath: $filePath);
		}

		// Remove fields we should never post.
		unset($data['id']);
		foreach($data as $key => $value) {
			if(str_starts_with(haystack: $key, needle: '_')) {
				unset($data[$key]);
			}
		}

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->attachmentMapper->createFromArray(object: $data));
		}

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$data['_schema'] = 'attachment';

		$returnData = $objectService->saveObject(
			data: $data,
			config: $dbConfig
		);

        return new JSONResponse($returnData);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
	 * @throws GuzzleException In case updating the file in NextCloud fails.
     */
    public function update(string|int $id, ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {
		$data = $this->request->getParams();

		// Remove fields we should never post.
		unset($data['id']);
		foreach($data as $key => $value) {
			if(str_starts_with(haystack: $key, needle: '_')) {
				unset($data[$key]);
			}
		}

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			return new JSONResponse($this->attachmentMapper->updateFromArray(id: (int) $id, object: $data));
		}


		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');


		$filters['_id'] = (string) $id;
		$returnData = $objectService->updateObject(
			filters: $filters,
			update: $data,
			config: $dbConfig
		);

		return new JSONResponse($returnData);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
	 * @throws GuzzleException In case deleting the file from NextCloud fails.
	 * @throws \OCP\DB\Exception In case deleting attachment from the NextCloud DB fails.
     */
    public function destroy(string|int $id, ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {
		$attachment = $this->show(id: $id, objectService: $objectService)->getData();
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$attachment = $attachment->jsonSerialize();
		}

		// Todo: are we sure this is the best way to do this (how do we save the full path to this file in nextCloud)
//		$publicationFolder = $this->fileService->getPublicationFolderName(
//			publicationId: $this->request->getHeader('Publication-Id'),
//			publicationTitle: $this->request->getHeader('Publication-Title')
//		);
//		$this->fileService->deleteFile(filePath: "Attachments/$publicationFolder" . $attachment['title'] . '.' . $attachment['extension']);
		$filePath = explode(separator: '/', string: $attachment['reference']);
		array_shift(array: $filePath);
		$filePath = implode(separator: '/', array: $filePath);
		$this->fileService->deleteFile(filePath: $filePath);

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$this->attachmentMapper->delete(entity: $this->attachmentMapper->find(id: (int) $id));

			return new JSONResponse([]);
		}

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = (string) $id;
		$returnData = $objectService->deleteObject(
			filters: $filters,
			config: $dbConfig
		);

		return new JSONResponse($returnData);
    }
}
