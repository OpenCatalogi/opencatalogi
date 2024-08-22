<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\IAppConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ConfigurationController extends Controller
{

	public function __construct(
		$appName,
		IAppConfig $config,
		IRequest $request
	) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->request = $request;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse
	{
		$data = [];
		$defaults = [
			'drcLocation' => '',
			'drcKey' => '',
			'orcLocation' => '',
			'orcKey' => '',
			'mongodbLocation' => '',
			'mongodbKey' => '',
			'mongodbCluster' => '',
			'elasticLocation' => '',
			'elasticKey' => '',
			'elasticIndex' => '',
			'organisationName' => 'my-organisation',
			'organisationOin' => '',
			'organisationPki' => ''
		];

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
	 * Handling the post request
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function create(): JSONResponse
	{
		$data = $this->request->getParams();

		try {
			foreach ($data as $key => $value) {
				$this->config->setValueString($this->appName, $key, $value);
				$data[$key] = $this->config->getValueString($this->appName, $key);
			}
			return new JSONResponse($data);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}
}
