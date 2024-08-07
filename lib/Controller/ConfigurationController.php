<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\IAppConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ConfigurationController extends Controller
{
	const TEST_ARRAY = [
		"5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f" => [
			"id" => "5137a1e5-b54d-43ad-abd1-4b5bff5fcd3f",
			"name" => "Configuration one",
			"summary" => "summary for one"
		],
		"7782b511-7034-4d49-a005-e827d5ae603f" => [
			"id" => "7782b511-7034-4d49-a005-e827d5ae603f",
			"name" => "Configuration two",
			"summary" => "summary for two"
		]
	];

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
			'organisationPki' => '',
			'adminUsername' => '',
			'adminPassword' => ''
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
