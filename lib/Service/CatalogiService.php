<?php
/**
 * Service for handling publication-related operations.
 *
 * Provides functionality for retrieving, saving, updating, and deleting publications,
 * as well as managing publication-related data and filters.
 *
 * @category Service
 * @package  OCA\OpenCatalogi\Service
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2024 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git_id>
 *
 * @link https://www.OpenCatalogi.nl
 */

namespace OCA\OpenCatalogi\Service;

use OCP\IRequest;
use OCP\IAppConfig;
use OCP\App\IAppManager;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use OCP\AppFramework\Http\JSONResponse;
use Exception;
use OCP\Common\Exception\NotFoundException;

/**
 * Service for handling publication-related operations.
 *
 * Provides functionality for retrieving, saving, updating, and deleting publications,
 * as well as managing publication-related data and filters.
 */
class CatalogiService
{

    /**
     * @var string $appName The name of the app
     */
    private string $appName;

    /**
     * @var array<string> List of available registers from catalogs
     */
    private array $availableRegisters = [];

    /**
     * @var array<string> List of available schemas from catalogs
     */
    private array $availableSchemas = [];


    /**
     * Constructor for PublicationService.
     *
     * @param IAppConfig       $config    App configuration interface
     * @param IRequest         $request   Request interface
     * @param IServerContainer $container Server container for dependency injection
     */
    public function __construct(
        private readonly IAppConfig $config,
        private readonly IRequest $request,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager,
    ) {
        $this->appName = 'opencatalogi';

    }//end __construct()


    /**
     * Attempts to retrieve the OpenRegister service from the container.
     *
     * @return mixed|null The OpenRegister service if available, null otherwise.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function getObjectService(): ?\OCA\OpenRegister\Service\ObjectService
    {
        if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
            $this->objectService = $this->container->get('OCA\OpenRegister\Service\ObjectService');

            return $this->objectService;
        }

        throw new \RuntimeException('OpenRegister service is not available.');

    }//end getObjectService()

    /**
     * Attempts to retrieve the OpenRegister service from the container.
     *
     * @return mixed|null The OpenRegister service if available, null otherwise.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function getFileService(): ?\OCA\OpenRegister\Service\FileService
    {
        if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
            $this->objectService = $this->container->get('OCA\OpenRegister\Service\FileService');

            return $this->objectService;
        }

        throw new \RuntimeException('OpenRegister service is not available.');

    }//end getObjectService()


    /**
     * Get register and schema combinations from catalogs.
     *
     * This method retrieves all catalogs (or a specific one if ID is provided),
     * extracts their registers and schemas, and stores them as general variables.
     *
     * @param  string|int|null $catalogId Optional ID of a specific catalog to filter by
     * @return array<string, array<string>> Array containing available registers and schemas
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function getCatalogFilters(null|string|int $catalogId = null): array
    {
        // Establish the default schema and register
        $schema   = $this->config->getValueString($this->appName, 'catalog_schema', '');
        $register = $this->config->getValueString($this->appName, 'catalog_register', '');

        $config = [];
        if ($catalogId !== null) {
            $catalogs = [$this->getObjectService()->find($catalogId)];
        } else {
            // Setup the config array
            $config['filters']['register'] = $register;
            $config['filters']['schema']   = $schema;
            // Get all catalogs or a specific one if ID is provided
            $catalogs = $this->getObjectService()->findAll($config);
        }

        // Initialize arrays to store unique registers and schemas
        $uniqueRegisters = [];
        $uniqueSchemas   = [];

        // Iterate over each catalog to extract registers and schemas
        foreach ($catalogs as $catalog) {
            $catalog = $catalog->jsonSerialize();
            // Check if 'registers' is an array and merge unique values
            if (isset($catalog['registers']) && is_array($catalog['registers'])) {
                $uniqueRegisters = array_merge($uniqueRegisters, $catalog['registers']);
            }

            // Check if 'schemas' is an array and merge unique values
            if (isset($catalog['schemas']) && is_array($catalog['schemas'])) {
                $uniqueSchemas = array_merge($uniqueSchemas, $catalog['schemas']);
            }
        }

        // Remove duplicate values and assign to class properties
        $this->availableRegisters = array_unique($uniqueRegisters);
        $this->availableSchemas   = array_unique($uniqueSchemas);

        return [
            'registers' => array_values($this->availableRegisters),
            'schemas'   => array_values($this->availableSchemas),
        ];

    }//end getCatalogFilters()


    /**
     * Get the list of available registers.
     *
     * @return array<string> List of available registers
     */
    public function getAvailableRegisters(): array
    {
        return $this->availableRegisters;

    }//end getAvailableRegisters()


    /**
     * Get the list of available schemas.
     *
     * @return array<string> List of available schemas
     */
    public function getAvailableSchemas(): array
    {
        return $this->availableSchemas;

    }//end getAvailableSchemas()


    /**
     * Private helper method to handle pagination of results.
     *
     * This method paginates the given results array based on the provided total, limit, offset, and page parameters.
     * It calculates the number of pages, sets the appropriate offset and page values, and returns the paginated results
     * along with metadata such as total items, current page, total pages, limit, and offset.
     *
     * @param array    $results The array of objects to paginate.
     * @param int|null $total   The total number of items (before pagination). Defaults to 0.
     * @param int|null $limit   The number of items per page. Defaults to 20.
     * @param int|null $offset  The offset of items. Defaults to 0.
     * @param int|null $page    The current page number. Defaults to 1.
     * @param array|null $facets    The already fetched facets. Defaults to empty array.
     *
     * @return array The paginated results with metadata.
     *
     * @phpstan-param  array<int, mixed> $results
     * @phpstan-return array<string, mixed>
     * @psalm-param    array<int, mixed> $results
     * @psalm-return   array<string, mixed>
     */
    private function paginate(array $results, ?int $total=0, ?int $limit=20, ?int $offset=0, ?int $page=1, ?array $facets = []): array
    {
        // Ensure we have valid values (never null)
        $total = max(0, ($total ?? 0));
        $limit = max(1, ($limit ?? 20));
        // Minimum limit of 1
        $offset = max(0, ($offset ?? 0));
        $page   = max(1, ($page ?? 1));
        // Minimum page of 1        // Calculate the number of pages (minimum 1 page)
        $pages = max(1, ceil($total / $limit));

        // If we have a page but no offset, calculate the offset
        if ($offset === 0) {
            $offset = (($page - 1) * $limit);
        }

        // If we have an offset but page is 1, calculate the page
        if ($page === 1 && $offset > 0) {
            $page = (floor($offset / $limit) + 1);
        }

        // If total is smaller than the number of results, set total to the number of results
        // @todo: this is a hack to ensure the pagination is correct when the total is not known. That sugjest that the underlaying count service has a problem that needs to be fixed instead
        if ($total < count($results)) {
            $total = count($results);
            $pages = max(1, ceil($total / $limit));
        }

        // Initialize the results array with pagination information
        $paginatedResults = [
            'results' => $results,
            'total'   => $total,
            'page'    => $page,
            'pages'   => $pages,
            'limit'   => $limit,
            'offset'  => $offset,
            'facets'  => $facets,
        ];

        // Add next/prev page URLs if applicable
        $currentUrl = $_SERVER['REQUEST_URI'];

        // Add next page link if there are more pages
        if ($page < $pages) {
            $nextPage = ($page + 1);
            $nextUrl  = preg_replace('/([?&])page=\d+/', '$1page='.$nextPage, $currentUrl);
            if (strpos($nextUrl, 'page=') === false) {
                $nextUrl .= (strpos($nextUrl, '?') === false ? '?' : '&').'page='.$nextPage;
            }

            $paginatedResults['next'] = $nextUrl;
        }

        // Add previous page link if not on first page
        if ($page > 1) {
            $prevPage = ($page - 1);
            $prevUrl  = preg_replace('/([?&])page=\d+/', '$1page='.$prevPage, $currentUrl);
            if (strpos($prevUrl, 'page=') === false) {
                $prevUrl .= (strpos($prevUrl, '?') === false ? '?' : '&').'page='.$prevPage;
            }

            $paginatedResults['prev'] = $prevUrl;
        }

        return $paginatedResults;

    }//end paginate()


    /**
     * Helper method to get configuration array from the current request
     *
     * @param string|null $register Optional register identifier
     * @param string|null $schema   Optional schema identifier
     * @param array|null  $ids      Optional array of specific IDs to filter
     *
     * @return array Configuration array containing:
     *               - limit: (int) Maximum number of items per page
     *               - offset: (int|null) Number of items to skip
     *               - page: (int|null) Current page number
     *               - filters: (array) Filter parameters
     *               - sort: (array) Sort parameters
     *               - search: (string|null) Search term
     *               - extend: (array|null) Properties to extend
     *               - fields: (array|null) Fields to include
     *               - unset: (array|null) Fields to exclude
     *               - register: (string|null) Register identifier
     *               - schema: (string|null) Schema identifier
     *               - ids: (array|null) Specific IDs to filter
     */
    private function getConfig(?string $register=null, ?string $schema=null, ?array $ids=null): array
    {
        $params = $this->request->getParams();

        unset($params['id']);
        unset($params['_route']);

        // Extract and normalize parameters
        $limit  = (int) ($params['limit'] ?? $params['_limit'] ?? 20);
        $offset = isset($params['offset']) ? (int) $params['offset'] : (isset($params['_offset']) ? (int) $params['_offset'] : null);
        $page   = isset($params['page']) ? (int) $params['page'] : (isset($params['_page']) ? (int) $params['_page'] : null);

        // If we have a page but no offset, calculate the offset
        if ($page !== null && $offset === null) {
            $offset = (($page - 1) * $limit);
        }

        $queries = ($params['queries'] ?? $params['_queries'] ?? []);
        if (is_string($queries) === true) {
            $queries = [$queries];
        }

        return [
            'limit'   => $limit,
            'offset'  => $offset,
            'page'    => $page,
            'filters' => $params,
            'sort'    => ($params['order'] ?? $params['_order'] ?? []),
            'search'  => ($params['_search'] ?? null),
            'extend'  => ($params['extend'] ?? $params['_extend'] ?? null),
            'fields'  => ($params['fields'] ?? $params['_fields'] ?? null),
            'unset'   => ($params['unset'] ?? $params['_unset'] ?? null),
            'queries' => $queries,
            'ids'     => $ids,
        ];

    }//end getConfig()


    /**
     * Retrieves a list of all objects for a specific register and schema
     *
     * This method returns a paginated list of objects that match the specified register and schema.
     * It supports filtering, sorting, and pagination through query parameters.
     *
     * @param ObjectService $objectService The object service
     *
     * @return JSONResponse A JSON response containing the list of objects
     *
     * @NoAdminRequired     *
     * @NoCSRFRequired
     */
    public function index(null|string|int $catalogId = null): JSONResponse
    {
        // Get config and fetch objects
        $config = $this->getConfig();

        // Get the context for the catalog
        $context                       = $this->getCatalogFilters($catalogId);
        //Vardump the context
        $config['filters']['register'] = $context['registers'];
        $config['filters']['schema']   = $context['schemas'];

        $objectService = $this->getObjectService();

        $objects = $objectService->findAll($config);
        
        // Filter out unwanted properties from the '@self' array in each object
        $filteredObjects = array_map(function ($object) {
            // Use jsonSerialize to get an array representation of the object
            $objectArray = $object->jsonSerialize();

            //@todo: a loggedin user should be able to see the full object
            if (isset($objectArray['@self']) && is_array($objectArray['@self'])) {
                $unwantedProperties = [
                    'schemaVersion', 'relations', 'locked', 'owner', 'folder',
                    'application', 'validation', 'retention',
                    'size', 'deleted'
                ];
                // Remove unwanted properties from the '@self' array
                $objectArray['@self'] = array_diff_key($objectArray['@self'], array_flip($unwantedProperties));
            }
            return $objectArray;
        }, $objects);
        

        // Get total count for pagination
        $total = $objectService->count($config);

        //@todo: fix facets currently breaks build
        //$facets = $objectService->getFacets(filters: [
        //    'register' => $config['filters']['register'],
        //    'schema'   => $config['filters']['schema'],
        //    '_queries' => $config['queries']
        //]);

        // Return paginated results
        return new JSONResponse($this->paginate(results: $filteredObjects, total: $total, limit: $config['limit'], offset: $config['offset'], page: $config['page'], facets: $facets));
    }//end index()

}//end class
