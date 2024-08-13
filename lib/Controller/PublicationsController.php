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
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Uid\Uuid;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Mpdf\Mpdf;
use ZipArchive;

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
        $fieldsToSearch = ['title', 'description', 'summary'];

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
	 */
	public function attachments(string|int $id, ObjectService $objectService, array|null $publication = null): JSONResponse
	{
		if ($publication === null) {
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
	 * Creates a pdf file containing all metadata of the given publication.
	 *
	 * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
	 * @param string|int $id The id of a Publication we want to create / update a pdf file for.
	 * @param bool|null $download If we should return a download response (true = default) or only generate and save the file in NextCloud (false).
	 * @param array|null $publication If we already have a publication body prevent extra database requests by passing it along.
	 *
	 * @return JSONResponse A JSONResponse ... todo
	 * @throws LoaderError|RuntimeError|SyntaxError|MpdfException|Exception
	 */
	private function createPublicationFile(ObjectService $objectService, string|int $id, ?bool $download = true, ?array $publication = null): JSONResponse
	{
		if ($publication === null) {
			$jsonResponse = $this->show(id: $id, objectService: $objectService);
			$publication = $jsonResponse->getData();
			if (is_array($publication) === true && isset($publication['error']) === true) {
				return new JSONResponse(data: $publication, statusCode: $jsonResponse->getStatus());
			}

			if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
				|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
			) {
				$publication = $publication->jsonSerialize();
			}
		}

		// Initialize Twig
		$loader = new FilesystemLoader(paths: 'lib/Templates', rootPath: '/var/www/html/apps-extra/opencatalogi');
		$twig = new Environment($loader);

		// Render the Twig template
		$html = $twig->render(name: 'publication.html.twig', context: ['publication' => $publication]);

		// Check if the directory exists, if not, create it
		if (file_exists(filename: '/tmp/mpdf') === false) {
			mkdir(directory: '/tmp/mpdf', recursive: true);
		}

		// Set permissions for the directory (ensure it's writable)
		chmod(filename: '/tmp/mpdf', permissions: 0777);

		// Initialize mPDF
		$mpdf = new Mpdf(config: ['tempDir' => '/tmp/mpdf']);

		// Write HTML to PDF
		$mpdf->WriteHTML(html: $html);

		// Output to a file
		$filename = "{$publication['title']}.pdf";
		$mpdf->Output(name: $filename, dest: Destination::FILE);

		// Create the Publicaties folder and the Publication specific folder.
		$this->fileService->createFolder(folderPath: 'Publicaties');
		$publicationFolder = $this->fileService->getPublicationFolderName(
			publicationId: $publication['id'],
			publicationTitle: $publication['title']
		);
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");

		// Save the uploaded file
		$filePath = "Publicaties/$publicationFolder/$filename";
		$this->fileService->deleteFile(filePath: $filePath);
		$created = $this->fileService->uploadFile(
			content: file_get_contents(filename: $filename),
			filePath: $filePath
		);

		// Create ShareLink
		$shareLink = $this->fileService->createShareLink(path: $filePath);

		if ($created === false) {
			return new JSONResponse(data: ['error' => "Failed to upload this file: $filePath to NextCloud"], statusCode: 500);
		}

		if ($download === true) {
			// Output directly to the browser
			$mpdf->Output(name: $filename, dest: Destination::DOWNLOAD);
		}

		// Remove tmp folder
		rmdir(directory: '/tmp/mpdf');

		return new JSONResponse(['downloadUrl' => "$shareLink/download"], 200);
	}


	/**
	 * todo:
	 *
	 * @param ObjectService $objectService
	 * @param string|int $id
	 *
	 * @return JSONResponse
	 * @throws LoaderError|MpdfException|RuntimeError|SyntaxError
	 */
	private function creatPublicationZip(ObjectService $objectService, string|int $id): JSONResponse
	{
		// Get the publication.
		$jsonResponse = $this->show(id: $id, objectService: $objectService);
		$publication = $jsonResponse->getData();
		if (is_array($publication) === true && isset($publication['error']) === true) {
			return new JSONResponse(data: $publication, statusCode: $jsonResponse->getStatus());
		}

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$publication = $publication->jsonSerialize();
		}

		// Update the publication .pdf file containing publication metadata.
		$jsonResponse = $this->createPublicationFile(objectService: $objectService, id: $id, download: false, publication: $publication);
		$publicationFile = $jsonResponse->getData();
		if (is_array($publicationFile) === true && isset($publicationFile['error']) === true) {
			return new JSONResponse(data: $publicationFile, statusCode: $jsonResponse->getStatus());
		}

		// Get all publication attachments.
		$attachments = $this->attachments(id: $id, objectService: $objectService, publication: $publication)->getData();
		if (isset($attachments['results']) === false) {
			return new JSONResponse(data: ['error' => "failed to get attachments for this publication: $id"], statusCode: 500);
		}

		// Temporary paths.
		$tempFolder = '/tmp/nextcloud_download_' . $publication['title'];
		$tempZip = '/tmp/publicatie_' . $publication['title'] . '.zip';

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
			file_put_contents(filename: "$tempFolder/{$publication['title']}.pdf", data: $file_content);
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

		// Create ZIP archive.
		$zip = new ZipArchive();
		if ($zip->open(filename: $tempZip, flags: ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
			$files = new RecursiveIteratorIterator(
				iterator: new RecursiveDirectoryIterator($tempFolder),
				mode: RecursiveIteratorIterator::LEAVES_ONLY
			);

			foreach ($files as $name => $file) {
				// Skip directories (they would be added automatically)
				if ($file->isDir() === false) {
					$filePath = $file->getRealPath();
					$relativePath = substr(string: $filePath, offset: strlen(string: $tempFolder) + 1);

					// Add file to zip
					$zip->addFile(filepath: $filePath, entryname: $relativePath);
				}
			}
			$zip->close();
		} else {
			return new JSONResponse(data: ['error' => "failed to create ZIP archive for this publication: $id"], statusCode: 500);
		}

		// Send the ZIP file to the client for download.
		header(header: 'Content-Type: application/zip');
		header(header: 'Content-disposition: attachment; filename=' . basename($tempZip));
		header(header: 'Content-Length: ' . filesize($tempZip));
		readfile(filename: $tempZip);

		// Cleanup temporary files.
		array_map(callback: 'unlink', array: glob(pattern: "$tempFolder/*.*"));
		rmdir(directory: $tempFolder);
		unlink(filename: $tempZip);

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
			'application/zip' => $this->creatPublicationZip(objectService: $objectService, id: $id),
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
    public function create(ObjectService $objectService, ElasticSearchService $elasticSearchService): JSONResponse
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
