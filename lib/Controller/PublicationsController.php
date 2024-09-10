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
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ValidationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\IAppConfig;
use OCP\IRequest;
use Symfony\Component\Uid\Uuid;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PublicationsController extends Controller
{

    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly PublicationMapper $publicationMapper,
		private readonly AttachmentMapper $attachmentMapper,
		private readonly IAppConfig $config,
		private readonly FileService $fileService
	)
    {
        parent::__construct($appName, $request);
    }

	private function insertNestedObjects(array $object, ObjectService $objectService, array $config): array
	{
		//@TODO keep in mind that unpublished objects should not be inserted, and that objects should be updated if a subobject is updated.
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

				if(
					is_array(value: $value) === true
					&& array_is_list(array: $value) === true
				) {
					$object[$key] = $this->insertNestedObjects(object: $value, objectService: $objectService, config: $config);
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
            $this->appName,
            'PublicationsIndex',
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
			$this->appName,
			'PublicationsIndex',
			[]
		);
	}

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService, SearchService $searchService): JSONResponse
    {
        $filters = $this->request->getParams();
		unset($filters['_route']);
        $fieldsToSearch = ['p.title', 'p.description', 'p.summary'];

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$searchParams = $searchService->createMySQLSearchParams(filters: $filters);
			$searchConditions = $searchService->createMySQLSearchConditions(filters: $filters, fieldsToSearch:  $fieldsToSearch);
			$sort = $searchService->createSortForMySQL(filters: $filters);
			$filters = $searchService->unsetSpecialQueryParams(filters: $filters);

			return new JSONResponse(['results'  => $this->publicationMapper->findAll(filters: $filters, searchConditions: $searchConditions, searchParams: $searchParams, sort: $sort)]);
		}

		$filters = $searchService->createMongoDBSearchFilter(filters: $filters, fieldsToSearch: $fieldsToSearch);
		$filters = $searchService->unsetSpecialQueryParams(filters: $filters);

		// @todo Fix mongodb sort
		// $sort = $searchService->createSortForMongoDB(filters: $filters);

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_schema'] = 'publication';

		$result = $objectService->findObjects(filters: $filters, config: $dbConfig);

        $results = ["results" => $result['documents']];
        return new JSONResponse($results);
    }

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 */
	public function attachments(string|int $id, ObjectService $objectService, ?array $publication = null): JSONResponse
	{
		if ($publication === null) {
			$publication = $this->getPublicationData(id: $id, objectService: $objectService);
			if ($publication instanceof JSONResponse) {
				return $publication;
			}
		}

		$attachments = $publication['attachments'];

		if ($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			return new JSONResponse(['results' => $this->attachmentMapper->findMultiple($attachments)]);
		}

		$filters = [];
		$filters['id']['$in'] = $attachments;

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_schema'] = 'attachment';

		$result = $objectService->findObjects(filters: $filters, config: $dbConfig);

		return new JSONResponse(['results' => $result['documents']]);
	}


	/**
	 * Gets a publication for the given id (if it exists) and returns its data as an array.
	 *
	 * @param string|int $id The id of a Publication we want to get a data array for.
	 * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
	 *
	 * @return array|JSONResponse An array containing all data of the publication or an error JSONResponse.
	 */
	private function getPublicationData(string|int $id, ObjectService $objectService): array|JSONResponse
	{
		$jsonResponse = $this->show($id, $objectService);
		$publication = $jsonResponse->getData();
		if (is_array($publication) === true && isset($publication['error']) === true) {
			return new JSONResponse(data: $publication, statusCode: $jsonResponse->getStatus());
		}

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$publication = $publication->jsonSerialize();
		}

		return $publication;
	}


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
		if ($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			try {
				return new JSONResponse($this->publicationMapper->find(id: (int) $id));
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
	 * Create/updates a file containing all metadata of a publication to NextCloud files, finds/creates a share link and returns it.
	 *
	 * @param string $filename The (tmp) filename of the file to store in NextCloud files.
	 * @param array $publication The publication data used to find/create the publication specific folder in NextCloud files.
	 *
	 * @return string|JSONResponse A share link url or an error JSONResponse.
	 * @throws Exception When a function reading or writing to NextCloud files goes wrong.
	 */
	private function saveFileToNextCloud(string $filename, array $publication): string|JSONResponse
	{
		// Create the Publicaties folder and the Publication specific folder.
		$this->fileService->createFolder(folderPath: 'Publicaties');
		$publicationFolder = $this->fileService->getPublicationFolderName(
			publicationId: $publication['id'],
			publicationTitle: $publication['title']
		);
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");

		// Save the file to NextCloud.
		$filePath = "Publicaties/$publicationFolder/$filename";
		$created = $this->fileService->updateFile(
			content: file_get_contents(filename: $filename),
			filePath: $filePath,
			createNew: true
		);

		if ($created === false) {
			return new JSONResponse(data: ['error' => "Failed to upload this file: $filePath to NextCloud"], statusCode: 500);
		}

		// Create ShareLink
		$share = $this->fileService->findShare(path: $filePath);
		if ($share !== null) {
			$shareLink = $this->fileService->getShareLink($share);
		} else {
			$shareLink = $this->fileService->createShareLink(path: $filePath);
		}

		return $shareLink;
	}


	/**
	 * Creates a pdf file containing all metadata of the given publication.
	 *
	 * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
	 * @param string|int $id The id of a Publication we want to create / update a pdf file for.
	 * @param array|null $options A few options for this function, "download" & "saveToNextCloud" can't be both false!
	 * "download" = If we should return a download response (true = default).
	 * "saveToNextCloud" = If we should create and save the file in NextCloud (true = default).
	 * "publication" = If we already have a publication body prevent extra database requests by passing it along.
	 *
	 * @return JSONResponse A JSONResponse for downloading the pdf file. Or a JSONResponse containing a downloadUrl for a nextCloud file. Or an error response.
	 * @throws LoaderError|RuntimeError|SyntaxError|MpdfException|Exception
	 */
	private function createPublicationFile(
		ObjectService $objectService, string|int $id,
		?array $options = [
			'download' => true,
			'saveToNextCloud' => true,
			'publication' => null
		]
	): JSONResponse
	{
		if ($options['download'] === false && $options['saveToNextCloud'] === false) {
			return new JSONResponse(data: ['error' => '$options "download" & "saveToNextCloud" for function
			createPublicationFile should not be both set to false'], statusCode: 500);
		}

		$publication = $options['publication'] ?? null;
		if ($publication === null) {
			$publication = $this->getPublicationData(id: $id, objectService: $objectService);
			if ($publication instanceof JSONResponse) {
				return $publication;
			}
		}

		// Create the PDF file using a twig template and publication data.
		$mpdf = $this->fileService->createPdf(twigTemplate: 'publication.html.twig', context: ['publication' => $publication]);

		// The filename.
		$filename = "{$publication['title']}.pdf";

		if (isset($options['saveToNextCloud']) === false || $options['saveToNextCloud'] === true) {
			// Output to a file.
			$mpdf->Output(name: $filename, dest: Destination::FILE);

			// Save the file in NextCloud.
			$shareLink = $this->saveFileToNextCloud(filename: $filename, publication: $publication);
			if ($shareLink instanceof JSONResponse) {
				return $shareLink;
			}
		}

		if (isset($options['download']) === false || $options['download'] === true) {
			// Output directly to the browser.
			$mpdf->Output(name: $filename, dest: Destination::DOWNLOAD);
		}

		// Remove tmp folder after mpdf->Output & $this->saveFileToNextCloud have been called.
		rmdir(directory: '/tmp/mpdf');

		if (isset($options['saveToNextCloud']) === false || $options['saveToNextCloud'] === true) {
			return new JSONResponse(data: [
					'downloadUrl' => "$shareLink/download",
					'filename' 	  => $filename
				], statusCode: 200
			);
		}
		return new JSONResponse([], 200);
	}


	/**
	 * Prepares the creation of a ZIP archive for a publication, by adding all folders & files we want in this zip
	 * to a $tempFolder that will be used as input for creating the actual ZIP archive later.
	 *
	 * @param string $tempFolder The tmp location used as input for creating the ZIP archive.
	 * @param array $attachments An array containing all Attachments (Bijlagen) for the Publication.
	 * @param array $publicationFile An array containing the downloadUrl and filename of the pdf file created that contains all metadata of the Publication.
	 *
	 * @return void
	 */
	private function prepareZip(string $tempFolder, array $attachments, array $publicationFile): void
	{
		// Create temporary directory
		if (file_exists(filename: $tempFolder) === false) {
			mkdir(directory: $tempFolder, recursive: true);
			if (count($attachments['results']) > 0) {
				mkdir(directory: "$tempFolder/Bijlagen", recursive: true);
			}
		}

		// Add .pdf file containing publication metadata.
		$file_content = file_get_contents(filename: $publicationFile['downloadUrl']);
		if ($file_content !== false) {
			file_put_contents(filename: "$tempFolder/{$publicationFile['filename']}", data: $file_content);
		}

		// Add all attachments in Bijlagen folder.
		foreach ($attachments['results'] as $attachment) {
			$attachment = $attachment->jsonSerialize();
			$file_content = file_get_contents(filename: $attachment['downloadUrl']);
			if ($file_content !== false) {
				$filePath = explode(separator: '/', string: $attachment['reference']);
				file_put_contents(filename: "$tempFolder/Bijlagen/".end(array: $filePath), data: $file_content);
			}
		}
	}


	/**
	 * Creates a ZIP archive containing a pdf file with all metadata of the publication for id = $id.
	 * Will also add all Attachments (Bijlagen) of this publication to this ZIP archive in a folder called 'Bijlagen'.
	 *
	 * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
	 * @param string|int $id The id of a Publication we want to download a ZIP archive for.
	 *
	 * @return JSONResponse A JSONResponse for downloading the ZIP archive. Or an error response.
	 * @throws LoaderError|MpdfException|RuntimeError|SyntaxError
	 */
	private function createPublicationZip(ObjectService $objectService, string|int $id): JSONResponse
	{
		// Get the publication.
		$publication = $this->getPublicationData(id: $id, objectService: $objectService);
		if ($publication instanceof JSONResponse) {
			return $publication;
		}

		// Update the publication .pdf file containing publication metadata.
		$jsonResponse = $this->createPublicationFile(objectService: $objectService, id: $id,
			options: ['download' => false, 'publication' => $publication]);
		if ($jsonResponse->getStatus() !== 200) {
			return $jsonResponse;
		}
		$publicationFile = $jsonResponse->getData();

		// Get all publication attachments.
		$attachments = $this->attachments(id: $id, objectService: $objectService, publication: $publication)->getData();
		if (isset($attachments['results']) === false) {
			return new JSONResponse(data: ['error' => "failed to get attachments for this publication: $id"], statusCode: 500);
		}

		// Temporary paths.
		$tempFolder = '/tmp/nextcloud_download_' . $publication['title'];
		$tempZip = '/tmp/publicatie_' . $publication['title'] . '.zip';

		// Prepare ZIP by creating a temp folder with everything we want in the ZIP archive.
		$this->prepareZip(tempFolder: $tempFolder, attachments: $attachments, publicationFile: $publicationFile);

		// Create the ZIP archive.
		$error = $this->fileService->createZip(inputFolder: $tempFolder, tempZip: $tempZip);
		if ($error !== null) {
			return new JSONResponse(data: ['error' => "failed to create ZIP archive for this publication: $id"], statusCode: 500);
		}

		// Return a download response containing the ZIP archive. And clean up temp files/folders.
		$this->fileService->downloadZip(tempZip: $tempZip, inputFolder: $tempFolder);

		return new JSONResponse([], 200);
	}


	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function download(string|int $id, ObjectService $objectService): JSONResponse
	{
		return match ($this->request->getHeader('Accept')) {
			'application/pdf' => $this->createPublicationFile(objectService: $objectService, id: $id),
			'application/zip' => $this->createPublicationZip(objectService: $objectService, id: $id),
			default => new JSONResponse(
				data: ['error' => 'Unsupported Accept header, please use [application/pdf] or [application/zip]'],
				statusCode: 400
			),
		};
	}


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(ObjectService $objectService, ElasticSearchService $elasticSearchService, ValidationService $validationService): JSONResponse
    {
		$data = $this->request->getParams();

		// Remove fields we should never post
		unset($data['id']);
		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}

		try {
			$data = $validationService->validatePublication($data);
		} catch (OCSBadRequestException|OCSNotFoundException $exception) {
			return new JSONResponse(data: ['message' => $exception->getMessage()], statusCode: 400);
		}

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$returnData = $this->publicationMapper->createFromArray($data);
			$returnData = $returnData->jsonSerialize();
			$dbConfig = [];
		} else {
			$data['_schema'] = 'publication';

			$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');
			$returnData = $objectService->saveObject(
				data: $data,
				config: $dbConfig
			);
		}
		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticLocation') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticKey') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticIndex') !== ''
			&& $returnData['status'] === 'published'
		) {
			$elasticConfig['location'] = $this->config->getValueString(app: $this->appName, key: 'elasticLocation');
			$elasticConfig['key'] 	   = $this->config->getValueString(app: $this->appName, key: 'elasticKey');
			$elasticConfig['index']    = $this->config->getValueString(app: $this->appName, key: 'elasticIndex');

			$returnData = $this->insertNestedObjects($returnData, $objectService, $dbConfig);

			$returnData = $elasticSearchService->addObject(object: $returnData, config: $elasticConfig);

		}

        // get post from requests
        return new JSONResponse($returnData);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string|int $id, ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {

		$data = $this->request->getParams();

		// Remove fields we should never post
		unset($data['id']);
		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}

		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$returnData = $this->publicationMapper->updateFromArray(id: (int) $id, object: $data);
			$returnData = $returnData->jsonSerialize();

			$dbConfig = [];
		} else {
			$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

			$filters['_id'] = (string) $id;
			$returnData = $objectService->updateObject(
				filters: $filters,
				update: $data,
				config: $dbConfig
			);
		}

		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticLocation') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticKey') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticIndex') !== ''
			&& $returnData['status'] === 'published'
		) {
			$elasticConfig['location'] = $this->config->getValueString(app: $this->appName, key: 'elasticLocation');
			$elasticConfig['key'] 	   = $this->config->getValueString(app: $this->appName, key: 'elasticKey');
			$elasticConfig['index']    = $this->config->getValueString(app: $this->appName, key: 'elasticIndex');

			$returnData = $this->insertNestedObjects($returnData, $objectService, $dbConfig);

			$returnData = $elasticSearchService->updateObject(id: $id, object: $returnData, config: $elasticConfig);

		}

		// get post from requests
		return new JSONResponse($returnData);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
    {
		if($this->config->hasKey($this->appName, 'mongoStorage') === false
			|| $this->config->getValueString($this->appName, 'mongoStorage') !== '1'
		) {
			$this->publicationMapper->delete($this->publicationMapper->find(id: (int) $id));

			$returnData = [];
		} else {
			$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
			$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
			$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

			$filters['_id'] = (string) $id;
			$returnData = $objectService->deleteObject(
				filters: $filters,
				config: $dbConfig
			);
		}

		if(
			$this->config->hasKey(app: $this->appName, key: 'elasticLocation') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticLocation') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticKey') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticKey') !== ''
			&& $this->config->hasKey(app: $this->appName, key: 'elasticIndex') === true
			&& $this->config->getValueString(app: $this->appName, key: 'elasticIndex') !== ''
			&& $returnData['status'] === 'published'
		) {
			$elasticConfig['location'] = $this->config->getValueString(app: $this->appName, key: 'elasticLocation');
			$elasticConfig['key'] 	   = $this->config->getValueString(app: $this->appName, key: 'elasticKey');
			$elasticConfig['index']    = $this->config->getValueString(app: $this->appName, key: 'elasticIndex');

			$returnData = $elasticSearchService->removeObject(id: $id, config: $elasticConfig);

		}

		// get post from requests
		return new JSONResponse($returnData);
    }
}
