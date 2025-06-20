<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PagesController
 *
 * Controller for handling page-related operations in the OpenCatalogi app.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben van der Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class PagesController extends Controller
{

    /**
     * PagesController constructor.
     *
     * @param string             $appName       The name of the app
     * @param IRequest           $request       The request object  
     * @param IAppConfig         $config        App configuration interface
     * @param ContainerInterface $container     Server container for dependency injection
     * @param IAppManager        $appManager    App manager for checking installed apps
     */
    public function __construct(
        $appName,
        IRequest $request,
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
     * Get the schema and register configuration for pages.
     *
     * @return array<string, string> Array containing schema and register configuration
     */
    private function getPageConfiguration(): array
    {
        // Get the page schema and register from configuration
        $schema   = $this->config->getValueString($this->appName, 'page_schema', '');
        $register = $this->config->getValueString($this->appName, 'page_register', '');

        return [
            'schema'   => $schema,
            'register' => $register,
        ];

    }//end getPageConfiguration()


    /**
     * Get all pages.
     *
     * @return JSONResponse The JSON response containing the list of pages
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        // Get page configuration from settings
        $pageConfig = $this->getPageConfiguration();


        // Build config for findAll to get pages
        $config = [
            'filters' => []
        ];

        // Add schema filter if configured
        if (!empty($pageConfig['schema'])) {
            $config['filters']['schema'] = $pageConfig['schema'];
        }

        // Add register filter if configured
        if (!empty($pageConfig['register'])) {
            $config['filters']['register'] = $pageConfig['register'];
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
     * Get a specific page by its slug.
     *
     * @param string $slug The slug of the page to retrieve
     *
     * @return JSONResponse The JSON response containing the page details
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string $slug): JSONResponse
    {
        // Get page configuration from settings
        $pageConfig = $this->getPageConfiguration();

        // Build config to find page by slug
        $config = [
            'filters' => [
                'slug' => $slug
            ]
        ];

        // Add schema filter if configured
        if (!empty($pageConfig['schema'])) {
            $config['filters']['schema'] = $pageConfig['schema'];
        }

        // Add register filter if configured
        if (!empty($pageConfig['register'])) {
            $config['filters']['register'] = $pageConfig['register'];
        }

        $pages = $this->getObjectService()->findAll($config);
        
        if (empty($pages)) {
            return new JSONResponse(['error' => 'Page not found'], 404);
        }

        // Return the first matching page
        $page = $pages[0] instanceof \OCP\AppFramework\Db\Entity ? $pages[0]->jsonSerialize() : $pages[0];
        
        return new JSONResponse($page);

    }//end show()


}//end class
