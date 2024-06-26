<?php

namespace OCA\OpenCatalog\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ConfigurationController extends Controller
{
    const TEST_ARRAY = [
        "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f" => [
            "id" => "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f"
            "name" => "one"
            "summery" => "summery for one"
        ],
        "4c3edd34-a90d-4d2a-8894-adb5836ecde8" => [
            "id" => "4c3edd34-a90d-4d2a-8894-adb5836ecde8"
            "name" => "two"
            "summery" => "summery for two"
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
    public function index(): JSONResponse {
        $params = [['name' => '1'],['name' => '2']] ;
        return new JSONResponse($params);
    }
    
    /**
	 * Handling the post request
	 * 
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(): JSONResponse {
        $params = [['name' => '1'],['name' => '2']] ;
        return new JSONResponse($params);
    }
}
