<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\CatalogiService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class CatalogiController
 * Controller for handling catalog-related operations in the OpenCatalogi app.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class CatalogiController extends Controller
{

    /**
     * CatalogiController constructor.
     *
     * @param string             $appName            The name of the app
     * @param IRequest           $request            The request object
     * @param CatalogiService    $catalogiService    The catalogi service
     * @param IAppConfig         $config             App configuration interface
     * @param ContainerInterface $container          Server container for dependency injection
     * @param IAppManager        $appManager         App manager for checking installed apps
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly CatalogiService $catalogiService,
        private readonly IAppConfig $config,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager
    ) {
        parent::__construct($appName, $request);

    }//end __construct()


    /**
     * Attempts to retrieve the OpenRegister ObjectService from the container.
     *
     * @return \OCA\OpenRegister\Service\ObjectService|null The OpenRegister ObjectService if available, null otherwise.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function getObjectService(): ?\OCA\OpenRegister\Service\ObjectService
    {
        if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
            return $this->container->get('OCA\OpenRegister\Service\ObjectService');
        }

        throw new \RuntimeException('OpenRegister service is not available.');

    }//end getObjectService()


    /**
     * Get the schema and register configuration for catalogs.
     *
     * @return array<string, string> Array containing schema and register configuration
     */
    private function getCatalogConfiguration(): array
    {
        // Get the catalog schema and register from configuration
        $schema   = $this->config->getValueString($this->appName, 'catalog_schema', '');
        $register = $this->config->getValueString($this->appName, 'catalog_register', '');

        return [
            'schema'   => $schema,
            'register' => $register,
        ];

    }//end getCatalogConfiguration()


    /**
     * Retrieve a list of publications based on all available catalogs.
     *
     * @param  string|int|null $catalogId Optional ID of a specific catalog to filter by
     * @return JSONResponse JSON response containing the list of publications and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        // Get catalog configuration from settings
        $catalogConfig = $this->getCatalogConfiguration();

        // Get all catalogs using configuration
        $config = [
            'filters' => []
        ];

        // Add schema filter if configured
        if (!empty($catalogConfig['schema'])) {
            $config['filters']['schema'] = $catalogConfig['schema'];
        }

        // Add register filter if configured
        if (!empty($catalogConfig['register'])) {
            $config['filters']['register'] = $catalogConfig['register'];
        }
        
        $result = $this->getObjectService()->findAll($config);
        
        // Convert objects to arrays
        $data = [
            'results' => array_map(function ($object) {
                return $object instanceof \OCP\AppFramework\Db\Entity ? $object->jsonSerialize() : $object;
            }, $result ?? []),
            'total' => count($result ?? [])
        ];

        return new JSONResponse($data);

    }//end index()


    /**
     * Retrieve a list of catalogs based on provided filters and parameters.
     *
     * @param  string|int $id The ID of the catalog to use as a filter
     * @return JSONResponse JSON response containing the list of catalogs and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string | int $id): JSONResponse
    {
        // Get all objects using the catalog's registers and schemas as filters
        $objects = $this->catalogiService->index($id);

        return $objects;

    }//end show()


}//end class
