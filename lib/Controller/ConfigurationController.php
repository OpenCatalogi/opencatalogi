<?php

namespace OCA\OpenCatalog\Controller;

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
		"4c3edd34-a90d-4d2a-8894-adb5836ecde8" => [
			"id" => "4c3edd34-a90d-4d2a-8894-adb5836ecde8",
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
		// Getting the config
			'drcLocation'=>'',
			'drcKey'=>'',
			'orcLocation'=>'',
			'orcKey'=>'',
			'elasticLocation'=>'',
			'elasticKey'=>'',
			'mongodbLocation'=>'',
			'mongodbKey'=>'',
			'mongodbCluster' => '',
			'organisationName'=>'my-organisation',
			'organisationOin'=> '',
			'organisationPki'=>''
		];

		// We should filter out unwanted values before this
		foreach($defaults as $key => $value){
			$data[$key] =  $this->config->getValueString('opencatalog', $key, $value);
		}

		return new JSONResponse($data);
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

		// We should filter out unwanted values before this
		foreach($data as $key => $value){
			$this->config->setValueString('opencatalog', $key, $value);
			$data[$key] =  $this->config->getValueString('opencatalog', $key);
		}

		return new JSONResponse($data);
	}
}
