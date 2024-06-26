<?php

namespace OCA\OpenCatalog\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class DashboardController extends Controller
{
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
			'index',
			[]
		);
	}

    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse {
        $params = [['name' => '1'],['name' => '2']] ;
        return new JSONResponse($params);
    }
}
