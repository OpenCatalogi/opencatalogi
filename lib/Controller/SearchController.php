<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\PublicationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class SearchController
 *
 * Controller for handling internal search-related operations in the OpenCatalogi app.
 * This controller is designed for internal/admin use and testing purposes.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben van der Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class SearchController extends Controller
{

    /**
     * SearchController constructor.
     *
     * @param string             $appName            The name of the app
     * @param IRequest           $request            The request object
     * @param PublicationService $publicationService The publication service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly PublicationService $publicationService
    ) {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of publications based on all available catalogs.
     *
     * This is an internal endpoint for testing and administrative purposes.
     * Unlike the public publications endpoint, this may include additional data
     * and is not subject to the same security restrictions.
     *
     * @param string|int|null $catalogId Optional ID of a specific catalog to filter by
     * @return JSONResponse JSON response containing the list of publications and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(?string $catalogId = null): JSONResponse
    {
        return $this->publicationService->index($catalogId);
    }

    /**
     * Retrieve a specific publication by its ID.
     *
     * This is an internal endpoint for testing and administrative purposes.
     *
     * @param string $id The ID of the publication to retrieve
     * @return JSONResponse JSON response containing the requested publication
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string $id): JSONResponse
    {
        return $this->publicationService->show(id: $id);
    }

    /**
     * Retrieve attachments/files of a publication.
     *
     * This is an internal endpoint for testing and administrative purposes.
     *
     * @param string $id Id of publication
     * @return JSONResponse JSON response containing the requested attachments/files.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function attachments(string $id): JSONResponse
    {
        return $this->publicationService->attachments(id: $id);
    }

    /**
     * Download files of a publication.
     *
     * This is an internal endpoint for testing and administrative purposes.
     *
     * @param string $id Id of publication
     * @return JSONResponse JSON response containing the download information.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function download(string $id): JSONResponse
    {
        return $this->publicationService->download(id: $id);
    }

    /**
     * Retrieves all objects that this publication references
     *
     * This method returns all objects that this publication uses/references. A -> B means that A (This publication) references B (Another object).
     * This is an internal endpoint for testing and administrative purposes.
     *
     * @param string $id The ID of the publication to retrieve relations for
     * @return JSONResponse A JSON response containing the related objects
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function uses(string $id): JSONResponse
    {
        return $this->publicationService->uses(id: $id);
    }

    /**
     * Retrieves all objects that use this publication
     *
     * This method returns all objects that reference (use) this publication. B -> A means that B (Another object) references A (This publication).
     * This is an internal endpoint for testing and administrative purposes.
     *
     * @param string $id The ID of the publication to retrieve uses for
     * @return JSONResponse A JSON response containing the referenced objects
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function used(string $id): JSONResponse
    {
        return $this->publicationService->used(id: $id);
    }

}//end class
