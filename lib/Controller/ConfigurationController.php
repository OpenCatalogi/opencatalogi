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
            "summery" => "summery for one"
        ],
        "4c3edd34-a90d-4d2a-8894-adb5836ecde8" => [
            "id" => "4c3edd34-a90d-4d2a-8894-adb5836ecde8",
            "name" => "Configuration two",
            "summery" => "summery for two"
        ]
    ];

	public function __construct(
        $appName, 
        IAppConfig $config,
        IRequest $request)
	{
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->request = $request;
	}
    
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse {

        // Getting the config
		$drcLocation = $this->config->getValueString('opencatalog', 'drc_location', '');
		$drcKey = $this->config->getValueString('opencatalog', 'drc_key', '');
		$orcLocation = $this->config->getValueString('opencatalog', 'orc_location', '');
		$orcKey = $this->config->getValueString('opencatalog', 'orc_key', '');
		$elasticLocation = $this->config->getValueString('opencatalog', 'elastic_location', '');
		$elasticKey = $this->config->getValueString('opencatalog', 'elastic_key', '');
		$organisationName = $this->config->getValueString('opencatalog', 'organisation_name', 'my-organisation');
		$organisationOin = $this->config->getValueString('opencatalog', 'organisation_oin', '');
		$organisationPki = $this->config->getValueString('opencatalog', 'organisation_pki', '');

		$data = [
			'drcLocation' => $drcLocation,
			'drcKey' => $drcKey,
			'orcLocation' => $orcLocation,
			'orcKey' => $orcKey,
			'elasticLocation' => $elasticLocation,
			'elasticKey' => $elasticKey,
			'organisationName' => $organisationName,
			'organisationPki' => $organisationPki,
			'organisationOin' => $organisationOin,
		];

        return new JSONResponse($data);
    }
    
    /**
	 * Handling the post request
	 * 
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(): JSONResponse {
		$data = $this->request->getParam();
        
		$drcLocation = $this->config->setValueString('opencatalog', 'drc_location', $data['drcLocation']);
		$drcKey = $this->config->setValueString('opencatalog', 'drc_key', $data['drcKey']);
		$orcLocation = $this->config->setValueString('opencatalog', 'orc_location', $data['orcLocation']);
		$orcKey = $this->config->setValueString('opencatalog', 'orc_key', $data['orcKey']);
		$elasticLocation = $this->config->setValueString('opencatalog', 'elastic_location', $data['elasticLocation']);
		$elasticKey = $this->config->setValueString('opencatalog', 'elastic_key', $data['elasticKey']);
		$organisationName = $this->config->setValueString('opencatalog', 'organisation_name', $data['organisationName']);
		$organisationOin = $this->config->setValueString('opencatalog', 'organisation_oin', $data['organisationOin']);
		$organisationPki = $this->config->setValueString('opencatalog', 'organisation_pki', $data['organisationPki']);
        
		$data = [
			'drcLocation' => $drcLocation,
			'drcKey' => $drcKey,
			'orcLocation' => $orcLocation,
			'orcKey' => $orcKey,
			'elasticLocation' => $elasticLocation,
			'elasticKey' => $elasticKey,
			'organisationName' => $organisationName,
			'organisationPki' => $organisationPki,
			'organisationOin' => $organisationOin,
		];

        return new JSONResponse($data);
    }
}
