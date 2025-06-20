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
 * Class GlossaryController
 *
 * Controller for handling glossary-related operations in the OpenCatalogi app.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben van der Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class GlossaryController extends Controller
{

    /**
     * GlossaryController constructor.
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
     * Get the schema and register configuration for glossary.
     *
     * @return array<string, string> Array containing schema and register configuration
     */
    private function getGlossaryConfiguration(): array
    {
        // Get the glossary schema and register from configuration
        $schema   = $this->config->getValueString($this->appName, 'glossary_schema', '');
        $register = $this->config->getValueString($this->appName, 'glossary_register', '');

        return [
            'schema'   => $schema,
            'register' => $register,
        ];

    }//end getGlossaryConfiguration()


    /**
     * Get all glossary terms.
     *
     * @return JSONResponse The JSON response containing the list of glossary terms
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        // Get glossary configuration from settings
        $glossaryConfig = $this->getGlossaryConfiguration();

        // Build config for findAll to get glossary terms
        $config = [
            'filters' => []
        ];

        // Add schema filter if configured
        if (!empty($glossaryConfig['schema'])) {
            $config['filters']['schema'] = $glossaryConfig['schema'];
        }

        // Add register filter if configured
        if (!empty($glossaryConfig['register'])) {
            $config['filters']['register'] = $glossaryConfig['register'];
        }

        $result = $this->getObjectService()->findAll($config);
        
        $data = [
            'results' => array_map(function ($object) {
                return $object instanceof \OCP\AppFramework\Db\Entity ? $object->jsonSerialize() : $object;
            }, $result ?? []),
            'total' => count($result ?? [])
        ];

        return new JSONResponse($data);

    }//end index()


    /**
     * Get a specific glossary term by its ID.
     *
     * @param string|int $id The ID of the glossary term to retrieve
     *
     * @return JSONResponse The JSON response containing the glossary term details
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string|int $id): JSONResponse
    {
        $glossaryTerm = $this->getObjectService()->find($id);
        
        $data = $glossaryTerm instanceof \OCP\AppFramework\Db\Entity ? $glossaryTerm->jsonSerialize() : $glossaryTerm;
        
        return new JSONResponse($data);

    }//end show()


}//end class
