<?php

namespace OCA\OpenCatalog\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class PublicationsController extends Controller
{
    const TEST_ARRAY = [
        "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f" => [
            "id" => "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f",
            "name" => "Input voor OpenCatalogi",
            "summery" => "Dit is een selectie van high-value datasets in DCAT-AP 2.0 standaard x"
        ]//,
        //"4c3edd34-a90d-4d2a-8894-adb5836ecde8" => [
        //    "id" => "4c3edd34-a90d-4d2a-8894-adb5836ecde8",
        //    "name" => "Publication two",
        //    "summery" => "summery for two"
        //]
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
    public function catalog(string $id): TemplateResponse 
    {
        // The TemplateResponse loads the 'main.php'
        // defined in our app's 'templates' folder.
        // We pass the $getParameter variable to the template
        // so that the value is accessible in the template.
        return new TemplateResponse(
            //Application::APP_ID,
            'opencatalog',
            'PublicationsIndex',
            []
        );
    }
    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse {
        $results = ["results"=>self::TEST_ARRAY] ;
        return new JSONResponse($results);
    }
    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string $id): JSONResponse {
        $result = self::TEST_ARRAY[$id] ;
        return new JSONResponse($result);
    }
    
    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(): JSONResponse {
        // get post from requests
        return new JSONResponse([]);
    }
    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string $id): JSONResponse {
        $result = self::TEST_ARRAY[$id] ;
        return new JSONResponse($result);
    }
    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string $id): JSONResponse {
        return new JSONResponse([]);
    }
}
