<?php
/**
 * Service for managing download-related operations.
 *
 * Provides functionality to create and manage publication files and archives, including
 * generating PDFs and ZIP files containing metadata and attachments, and storing files
 * in NextCloud.
 *
 * @category Service
 * @package  OCA\OpenCatalogi\Service
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2024 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git_id>
 *
 * @link https://www.OpenCatalogi.nl
 */

namespace OCA\OpenCatalogi\Service;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http\JSONResponse;
use OCA\OpenCatalogi\Service\FileService;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Mpdf\MpdfException;
use Exception;

/**
 * Service for managing download-related operations.
 *
 * Provides functionality to create and manage publication files and archives, including
 * generating PDFs and ZIP files containing metadata and attachments, and storing files
 * in NextCloud.
 */
class DownloadService
{


    /**
     * Constructor for DownloadService
     *
     * @param FileService $fileService The file service for handling file operations
     */
    public function __construct(
        private readonly FileService $fileService
    ) {

    }//end __construct()


    /**
     * Creates a pdf file containing all metadata of the given publication.
     *
     * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
     * @param string|int    $id            The id of a Publication we want to create / update a pdf file for.
     * @param array|null    $options       A few options for this function, "download" & "saveToNextCloud" can't be both false!
     *                                     "download" = If we should return a download response (true = default).
     *                                     "saveToNextCloud" = If we should create and save the file in NextCloud (true =
     *                                     default). "publication" = If we already have a publication body prevent extra
     *                                     database requests by passing it along.                                   database requests by passing it along.
     *
     * @return JSONResponse A JSONResponse for downloading the pdf file. Or a JSONResponse containing a downloadUrl for a nextCloud file. Or an error response.
     * @throws LoaderError|RuntimeError|SyntaxError|MpdfException|Exception
     */
    public function createPublicationFile(
        ObjectService $objectService,
        string|int $id,
        ?array $options=[
            'download'        => true,
            'saveToNextCloud' => true,
            'publication'     => null,
        ]
    ): JSONResponse {
        // Validate options
        if ($options['download'] === false && $options['saveToNextCloud'] === false) {
            return new JSONResponse(
                data: ['error' => '$options "download" & "saveToNextCloud" for function createPublicationFile should not be both set to false'],
                statusCode: 500
            );
        }

        // Get publication data if not provided
        $publication = ($options['publication'] ?? $this->getPublicationData($id, $objectService));
        if ($publication instanceof JSONResponse) {
            return $publication;
        }

        // Create the PDF file using a twig template and publication data
        $mpdf = $this->fileService->createPdf('publication.html.twig', ['publication' => $publication]);

        $filename = "{$publication['title']}.pdf";

        // Save to NextCloud if option is set
        $shareLink = null;
        if ($options['saveToNextCloud'] ?? true) {
            $mpdf->Output($filename, Destination::FILE);
            $shareLink = $this->saveFileToNextCloud($filename, $publication);
            if ($shareLink instanceof JSONResponse) {
                return $shareLink;
            }
        }

        // Download if option is set
        if ($options['download'] ?? true) {
            $mpdf->Output($filename, Destination::DOWNLOAD);
        }

        // Clean up temporary files
        rmdir('/tmp/mpdf');

        // Return download URL if saved to NextCloud
        if ($options['saveToNextCloud'] ?? true) {
            return new JSONResponse(
                [
                    'downloadUrl' => "$shareLink/download",
                    'filename'    => $filename,
                ],
                200
            );
        }

        return new JSONResponse([], 200);

    }//end createPublicationFile()


    /**
     * Gets a publication and returns it as serialized array.
     *
     * @param string|int    $id            The id of a publication.
     * @param ObjectService $objectService The objectService.
     *
     * @return array|JSONResponse The publication found as array or an error JSONResponse.
     */
    private function getPublicationData(string|int $id, ObjectService $objectService): array|JSONResponse
    {
        try {
            return $objectService->getObject('publication', $id);
        } catch (NotFoundExceptionInterface | MultipleObjectsReturnedException | ContainerExceptionInterface | DoesNotExistException $e) {
            return new JSONResponse(data: ['error' => $e->getMessage()], statusCode: 500);
        }

    }//end getPublicationData()


    /**
     * Create/updates a file containing all metadata of a publication to NextCloud files, finds/creates a share link and returns it.
     *
     * @param string $filename    The (tmp) filename of the file to store in NextCloud files
     * @param array  $publication The publication data used to find/create the publication specific folder in NextCloud files
     *
     * @return string|JSONResponse A share link url or an error JSONResponse
     * @throws Exception When a function reading or writing to NextCloud files goes wrong
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function saveFileToNextCloud(string $filename, array $publication): string|JSONResponse
    {
        // Create the Publicaties folder and the Publication specific folder
        $this->fileService->createFolder(folderPath: 'Publicaties');
        $publicationFolder = $this->fileService->getPublicationFolderName(
            publicationId: $publication['id'],
            publicationTitle: $publication['title']
        );
        $this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");

        // Save the file to NextCloud
        $filePath = "Publicaties/$publicationFolder/$filename";
        $created  = $this->fileService->updateFile(
            content: file_get_contents(filename: $filename),
            filePath: $filePath,
            createNew: true
        );

        // Check if file creation was successful
        if ($created === false) {
            return new JSONResponse(data: ['error' => "Failed to upload this file: $filePath to NextCloud"], statusCode: 500);
        }

        // Create or find ShareLink
        $share = $this->fileService->findShare(path: $filePath);
        if ($share !== null) {
            $shareLink = $this->fileService->getShareLink($share);
        } else {
            $shareLink = $this->fileService->createShareLink(path: $filePath);
        }

        return $shareLink;

    }//end saveFileToNextCloud()


    /**
     * Prepares the creation of a ZIP archive for a publication
     *
     * @param string $tempFolder      The tmp location used as input for creating the ZIP archive.
     * @param array  $attachments     An array containing all Attachments (Bijlagen) for the Publication.
     * @param array  $publicationFile An array containing the downloadUrl and filename of the pdf file created that contains all metadata of the Publication.
     *
     * @return void
     */
    private function prepareZip(string $tempFolder, array $attachments, array $publicationFile): void
    {
        // Create temporary directory structure
        if (file_exists($tempFolder) === false) {
            mkdir($tempFolder, recursive: true);
            if (count($attachments) > 0) {
                mkdir("$tempFolder/Bijlagen", recursive: true);
            }
        }

        // Add publication metadata PDF file
        $file_content = file_get_contents($publicationFile['downloadUrl']);
        if ($file_content !== false) {
            file_put_contents("$tempFolder/{$publicationFile['filename']}", $file_content);
        }

        // Add all attachments to Bijlagen folder
        foreach ($attachments as $attachment) {
            $attachment   = $attachment->jsonSerialize();
            $file_content = file_get_contents($attachment['downloadUrl']);
            if ($file_content !== false) {
                $filePath = explode('/', $attachment['reference']);
                file_put_contents("$tempFolder/Bijlagen/".end($filePath), $file_content);
            }
        }

    }//end prepareZip()


    /**
     * Creates a ZIP archive containing a pdf file with all metadata of the publication and its attachments
     *
     * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
     * @param string|int    $id            The id of a Publication we want to download a ZIP archive for.
     *
     * @return JSONResponse A JSONResponse for downloading the ZIP archive. Or an error response.
     * @throws LoaderError|MpdfException|RuntimeError|SyntaxError
     */
    public function createPublicationZip(ObjectService $objectService, string|int $id): JSONResponse
    {
        // Get the publication data
        $publication = $this->getPublicationData($id, $objectService);
        if ($publication instanceof JSONResponse) {
            return $publication;
        }

        // Create/update the publication PDF file
        $jsonResponse = $this->createPublicationFile(
            $objectService,
            $id,
            [
                'download'    => false,
                'publication' => $publication,
            ]
        );
        if ($jsonResponse->getStatus() !== 200) {
            return $jsonResponse;
        }

        $publicationFile = $jsonResponse->getData();

        // Get all publication attachments
        $attachments = $this->publicationAttachments($id, $objectService);
        if ($attachments instanceof JSONResponse) {
            return $attachments;
        }

        // Set up temporary paths
        $tempFolder = '/tmp/nextcloud_download_'.$publication['title'];
        $tempZip    = '/tmp/publicatie_'.$publication['title'].'.zip';

        // Prepare ZIP contents
        $this->prepareZip($tempFolder, $attachments, $publicationFile);

        // Create the ZIP archive
        $error = $this->fileService->createZip($tempFolder, $tempZip);
        if ($error !== null) {
            return new JSONResponse(['error' => "Failed to create ZIP archive for this publication: $id"], 500);
        }

        // Return a download response and clean up temp files/folders
        $this->fileService->downloadZip($tempZip, $tempFolder);

        return new JSONResponse([], 200);

    }//end createPublicationZip()


    /**
     * Gets all attachments for a publication.
     *
     * @param string|int    $id            The id of a publication.
     * @param ObjectService $objectService The objectService.
     *
     * @return array|JSONResponse An array containing all attachments for the publication or an error JSONResponse.
     */
    public function publicationAttachments(string|int $id, ObjectService $objectService): array|JSONResponse
    {
        // Fetch attachment objects
        try {
            // Fetch the publication object by its ID
            $object = $objectService->getObject(objectType: 'publication', id: $id);

            // Fetch attachment objects
            return $objectService->getMultipleObjects(objectType: 'attachment', ids: $object['attachments']);
        } catch (NotFoundExceptionInterface | MultipleObjectsReturnedException | ContainerExceptionInterface | DoesNotExistException $e) {
            return new JSONResponse(data: ['error' => $e->getMessage()], statusCode: 500);
        }

    }//end publicationAttachments()


}//end class
