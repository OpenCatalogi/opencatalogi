<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\IAppConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCA\OpenCatalogi\Service\ObjectService;

/**
 * Class SettingsController
 *
 * Controller for handling settings-related operations in the OpenCatalogi app.
 */
class SettingsController extends Controller
{

	/**
	 * SettingsController constructor.
	 *
	 * @param string $appName The name of the app
	 * @param IAppConfig $config The app configuration
	 * @param IRequest $request The request object
	 * @param ObjectService $objectService The object service
	 */
	public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Retrieve the current settings.
	 *
	 * @return JSONResponse JSON response containing the current settings
	 *
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse
	{
		// Initialize the data array
		$data = [];
		$data['objectTypes'] = ['attachment', 'catalog', 'listing', 'publicationtype', 'organization', 'publication', 'theme', 'page', 'menu'];
		$data['openRegisters'] = false;
		$data['availableRegisters'] = [];

		// Check if the OpenRegister service is available
		$openRegisters = $this->objectService->getOpenRegisters();
		if ($openRegisters !== null) {
			$data['openRegisters'] = true;
			$data['availableRegisters'] = $openRegisters->getRegisters();
		}

		// Define the default values for the object types
		$defaults = [
			'attachment_source' => 'internal',
			'attachment_schema' => '',
			'attachment_register' => '',
			'catalog_source' => 'internal',
			'catalog_schema' => '',
			'catalog_register' => '',
			'listing_source' => 'internal',
			'listing_schema' => '',
			'listing_register' => '',
			'publicationtype_source' => 'internal',
			'publicationtype_schema' => '',
			'publicationtype_register' => '',
			'organization_source' => 'internal',
			'organization_schema' => '',
			'organization_register' => '',
			'publication_source' => 'internal',
			'publication_schema' => '',
			'publication_register' => '',
			'theme_source' => 'internal',
			'theme_schema' => '',
			'theme_register' => '',
			'page_source' => 'internal',
			'page_schema' => '',
			'page_register' => '',
			'menu_source' => 'internal',
			'menu_schema' => '',
			'menu_register' => '',
		];

		// Get the current values for the object types from the configuration
		try {
			foreach ($defaults as $key => $value) {
				$data[$key] = $this->config->getValueString($this->appName, $key, $value);
			}
			return new JSONResponse($data);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Handle the post request to update settings.
	 *
	 * @return JSONResponse JSON response containing the updated settings
	 *
	 * @NoCSRFRequired
	 */
	public function create(): JSONResponse
	{
		// Get all parameters from the request
		$data = $this->request->getParams();

		try {
			// Update each setting in the configuration
			foreach ($data as $key => $value) {
				$this->config->setValueString($this->appName, $key, $value);
				// Retrieve the updated value to confirm the change
				$data[$key] = $this->config->getValueString($this->appName, $key);
			}
			return new JSONResponse($data);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}
}
