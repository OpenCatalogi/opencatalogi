<?php

namespace OCA\OpenCatalog\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class MetaDataController extends Controller
{
    const TEST_ARRAY = [
        "6892aeb1-d92d-4da5-ad41-f1c3278f40c2" => [
            "id" => "6892aeb1-d92d-4da5-ad41-f1c3278f40c2",
            "name" => "MetaData one",
            "summary" => "summary for one"
        ],
        "a375d626-ffe8-4a26-a024-1ad452d1b931" => [
            "id" => "a375d626-ffe8-4a26-a024-1ad452d1b931",
            "name" => "MetaData two",
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
            'metaDataIndex',
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
