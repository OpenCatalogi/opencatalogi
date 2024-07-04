<?php

namespace OCA\OpenCatalog\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class DirectoryController extends Controller
{
    const TEST_ARRAY = [
        "64996753-5109-4396-9f07-17040d7fb137" => [
            "id" => "64996753-5109-4396-9f07-17040d7fb137",
            "name" => "Directory one",
            "summary" => "summary for one"
        ],
        "0dcb7be0-ce06-4453-9ea7-6d66f67aa4ea" => [
            "id" => "0dcb7be0-ce06-4453-9ea7-6d66f67aa4ea",
            "name" => "Directory two",
            "summary" => "summary for two"
        ]
    ];

    public function __construct($appName, IRequest $request)
    {
        parent::__construct($appName, $request);
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
            'opencatalog',
            'DirectoryIndex',
            []
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        $results = ["results" => self::TEST_ARRAY];
        return new JSONResponse($results);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string $id): JSONResponse
    {
        $result = self::TEST_ARRAY[$id];
        return new JSONResponse($result);
    }


    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(): JSONResponse
    {
        // get post from requests
        return new JSONResponse([]);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string $id): JSONResponse
    {
        $result = self::TEST_ARRAY[$id];
        return new JSONResponse($result);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string $id): JSONResponse
    {
        return new JSONResponse([]);
    }
}
