<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class MetaDataController extends Controller
{
    const TEST_ARRAY = [
        "6892aeb1-d92d-4da5-ad41-f1c3278f40c2" => [
            "id" => "6892aeb1-d92d-4da5-ad41-f1c3278f40c2",
            "title" => "Woo verzoek en -besluit",
            "description" => "Woo verzoek",
            "version" => "0.0.1",
            "properties" =>  '{
        "id": {
            "type": "string"
        },
        "titel": {
            "type": "string"
        },
        "beschrijving": {
            "type": "string"
        },
        "samenvatting": {
            "type": "string"
        },
        "categorie": {
            "type": "string",
            "required": true
        },
        "gepubliceerd": {
            "type": "boolean",
            "default": true
        },
        "portalUrl": {
            "type": "string",
            "format": "url"
        },
        "publicatiedatum": {
            "description": "Publicatiedatum van een Woo object is nooit in de toekomst.",
            "type": "string",
            "maxDate": "now",
            "required": true
        },
        "organisatie": {
            "type": "object",
            "$ref": "https://commongateway.nl/woo.organisatie.schema.json",
            "format": "json",
            "cascadeDelete": true
        },
        "bijlagen": {
            "type": "array",
            "items": {
                "$ref": "https://commongateway.nl/woo.bijlage.schema.json"
            },
            "format": "json",
            "cascadeDelete": true
        },
        "metadata": {
            "type": "object",
            "$ref": "https://commongateway.nl/woo.metadata.schema.json",
            "format": "json",
            "cascadeDelete": true
        },
        "themas": {
            "type": "array",
            "items": {
                "$ref": "https://commongateway.nl/woo.thema.schema.json"
            },
            "format": "json",
            "cascadeDelete": true
        }
    }'
        ],
        "a375d626-ffe8-4a26-a024-1ad452d1b931" => [
            "id" => "a375d626-ffe8-4a26-a024-1ad452d1b931",
            "title" => "Convenant",
            "descriptiont" => "Woo Convenant",
            "version" => "0.0.1",
			"properties" => '{
        "id": {
            "type": "string"
        },
        "titel": {
            "type": "string"
        },
        "beschrijving": {
            "type": "string"
        },
        "samenvatting": {
            "type": "string"
        },
        "categorie": {
            "type": "string",
            "required": true
        },
        "gepubliceerd": {
            "type": "boolean",
            "default": true
        },
        "portalUrl": {
            "type": "string",
            "format": "url"
        },
        "publicatiedatum": {
            "description": "Publicatiedatum van een Woo object is nooit in de toekomst.",
            "type": "string",
            "maxDate": "now",
            "required": true
        },
        "organisatie": {
            "type": "object",
            "$ref": "https://commongateway.nl/woo.organisatie.schema.json",
            "format": "json",
            "cascadeDelete": true
        },
        "bijlagen": {
            "type": "array",
            "items": {
                "$ref": "https://commongateway.nl/woo.bijlage.schema.json"
            },
            "format": "json",
            "cascadeDelete": true
        },
        "metadata": {
            "type": "object",
            "$ref": "https://commongateway.nl/woo.metadata.schema.json",
            "format": "json",
            "cascadeDelete": true
        },
        "themas": {
            "type": "array",
            "items": {
                "$ref": "https://commongateway.nl/woo.thema.schema.json"
            },
            "format": "json",
            "cascadeDelete": true
        }
    }'
        ]
    ];

    public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config
	)
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
            $this->appName,
            'metaDataIndex',
            []
        );
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
	public function index(ObjectService $objectService): JSONResponse
	{
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters = $this->request->getParams();

		foreach($filters as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($filters[$key]);
			}
		}



		$filters['_schema'] = 'metadata';

		$result = $objectService->findObjects(filters: $filters, config: $dbConfig);

		$results = ["results" => $result['documents']];
		return new JSONResponse($results);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(string $id, ObjectService $objectService): JSONResponse
	{
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = $id;

		$result = $objectService->findObject(filters: $filters, config: $dbConfig);

		return new JSONResponse($result);
	}


	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function create(ObjectService $objectService): JSONResponse
	{
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$data = $this->request->getParams();

		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}

		$data['_schema'] = 'metadata';

		$returnData = $objectService->saveObject(
			data: $data,
			config: $dbConfig
		);

		// get post from requests
		return new JSONResponse($returnData);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function update(string $id, ObjectService $objectService): JSONResponse
	{
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$data = $this->request->getParams();

		foreach($data as $key => $value) {
			if(str_starts_with($key, '_')) {
				unset($data[$key]);
			}
		}
		if (isset($data['id'])) {
			unset( $data['id']);
		}

		$filters['_id'] = $id;
		$returnData = $objectService->updateObject(
			filters: $filters,
			update: $data,
			config: $dbConfig
		);

		// get post from requests
		return new JSONResponse($returnData);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function destroy(string $id, ObjectService $objectService): JSONResponse
	{
		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		$filters['_id'] = $id;
		$returnData = $objectService->deleteObject(
			filters: $filters,
			config: $dbConfig
		);

		// get post from requests
		return new JSONResponse($returnData);
	}
}
