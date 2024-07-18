<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class DashboardController extends Controller
{
	const TEST_ARRAY = [
		"d021c5ff-a254-4114-a1fb-7a18db152270" => [
			"id" => "d021c5ff-a254-4114-a1fb-7a18db152270",
			"name" => "Dashboard one",
			"summary" => "summary for one"
		],
		"79c02b33-78ba-4d65-aabd-ff9aae6654f7" => [
			"id" => "79c02b33-78ba-4d65-aabd-ff9aae6654f7",
			"name" => "Dashboard two",
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
			$this->appName,
			'index',
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
}
