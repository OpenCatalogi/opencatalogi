<?php
/**
 * Service for managing and synchronizing directories and listings.
 *
 * This service facilitates operations related to directories, catalogs, and listings.
 * It supports synchronization with external directories, validation and updates
 * of listings, and integration with publication types.
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

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use JsonSerializable;
use OCA\OpenCatalogi\Service\BroadcastService;
use OCA\OpenCatalogi\Exception\DirectoryUrlException;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Service for managing and synchronizing directories and listings.
 *
 * This service facilitates operations related to directories, catalogs, and listings.
 * It supports synchronization with external directories, validation and updates
 * of listings, and integration with publication types.
 */
class DirectoryService
{

    /**
     * @var string The name of the app
     */
    private string $appName = 'opencatalogi';

    /**
     * @var Client The HTTP client for making requests
     */
    private Client $client;

    /**
     * @var array The list of external publication types that are used by this instance
     */
    private array $externalPublicationTypes = [];


    /**
     * Constructor for DirectoryService
     *
     * @param IURLGenerator     $urlGenerator     URL generator interface
     * @param IAppConfig        $config           App configuration interface
     * @param ContainerInterface $container       Server container for dependency injection
     * @param IAppManager       $appManager       App manager for checking installed apps
     * @param BroadcastService  $broadcastService Broadcast service for broadcasting
     */
    public function __construct(
        private readonly IURLGenerator $urlGenerator,
        private readonly IAppConfig $config,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager,
        private readonly BroadcastService $broadcastService,
    ) {
        $this->client = new Client([]);

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
     * Get publication types from external directories
     *
     * @return array Array of publication types
     * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function getExternalPublicationTypes(): array
    {
        $result                         = $this->getObjectService()->findAll(['filters' => ['schema' => 'publicationType']]);
        $result['results']              = array_map(function ($object) {
            return $object instanceof Entity === true ? $object->jsonSerialize() : $object;
        }, $result['results'] ?? []);

        return $result;

    }//end getExternalPublicationTypes()


    /**
     * Convert a listing object or array to a directory array
     *
     * @param  array $listing The listing array to convert
     * @return array The converted directory array
     */
    private function getDirectoryFromListing(array $listing): array
    {
        // Serialize the listing if it's a Listing object
        if ($listing instanceof JsonSerializable) {
            $listing = $listing->jsonSerialize();
        }

        // Set id to uuid @todo this breaks stuff when trying to find and update a listing
        // $listing['id'] = $listing['uuid'];        // Remove unneeded fields
        unset(
            $listing['status'],
            $listing['lastSync'],
            $listing['default'],
            $listing['available'],
            $listing['statusCode'],
            // $listing['uuid'], //@todo this breaks stuff when trying to find and update a listing
            $listing['hash']
        );

        // Process publication types
        if (isset($listing['publicationTypes']) && is_array($listing['publicationTypes'])) {
            foreach ($listing['publicationTypes'] as &$publicationType) {
                // Convert publicationType to array if it's an object
                if ($publicationType instanceof JsonSerializable) {
                    $publicationType = $publicationType->jsonSerialize();
                }

                // set listed and owner to false by default
                $publicationType['listed'] = false;
                $publicationType['owner']  = false;

                // check if this publication type is used by this instance
                if (isset($publicationType['source'])) {
                    // Get all external publication types used by this instance
                    $externalPublicationTypes = $this->getExternalPublicationTypes();

                    // Filter external types to find matches with the current publication type's source
                    $matchingTypes = array_filter(
                        $externalPublicationTypes,
                        function ($externalType) use ($publicationType) {
                                       // Check if the external type has a source and if it matches the current publication type's source
                                       return isset($externalType['source']) && $externalType['source'] === $publicationType['source'];
                                   }
                    );

                    // Set 'listed' to true if there are any matching types, false otherwise
                    $publicationType['listed'] = !empty($matchingTypes);
                }
            }//end foreach
        }//end if

        // TODO: This should be mapped to the stoplight documentation
        return $listing;

    }//end getDirectoryFromListing()


    /**
     * Convert a catalog object or array to a directory array
     *
     * @param  array $catalog The catalog array to convert
     * @param  array $schemaLookup Lookup array of schemas keyed by ID
     * @return array The converted directory array
     */
    private function getDirectoryFromCatalog(array $catalog, array $schemaLookup = []): array
    {
        // Serialize the catalog if it's a Catalog object
        if ($catalog instanceof JsonSerializable) {
            $catalog = $catalog->jsonSerialize();
        }

        // Set id to uuid if it's not a valid UUID and uuid field exists with a valid UUID
        if ((!isset($catalog['id']) && isset($catalog['uuid'])) 
            || (!Uuid::isValid($catalog['id']) && isset($catalog['uuid']) && Uuid::isValid($catalog['uuid']))
        ) {
            $catalog['id'] = $catalog['uuid'];
        }

        // Remove unneeded fields
        unset(
            $catalog['image'], 
            $catalog['uuid'], 
            $catalog['registers'], 
            $catalog['extend'], 
            $catalog['filters'],
        );

        // If '@self' exists and is an array, remove unwanted properties
        if (isset($catalog['@self']) && is_array($catalog['@self'])) {
            $allowedProperties = ['id', 'updated', 'created', 'published', 'depublished'];
            $catalog['@self'] = array_intersect_key($catalog['@self'], array_flip($allowedProperties));
        }

        // Reorder catalog to have '@self' as the first property and 'id' as the second
        $orderedCatalog = [];
        if (isset($catalog['@self'])) {
            $orderedCatalog['@self'] = $catalog['@self'];
        }
        if (isset($catalog['id'])) {
            $orderedCatalog['id'] = $catalog['id'];
        }

        // Add remaining properties
        foreach ($catalog as $key => $value) {
            if (!isset($orderedCatalog[$key])) {
                $orderedCatalog[$key] = $value;
            }
        }

        // Replace schema IDs with schema objects if lookup array is provided
        if (!empty($schemaLookup) && isset($orderedCatalog['schemas']) && is_array($orderedCatalog['schemas'])) {
            $schemaObjects = [];
            foreach ($orderedCatalog['schemas'] as $schemaId) {
                if (isset($schemaLookup[$schemaId])) {
                    $schema = $schemaLookup[$schemaId];
                    
                    // Remove security-sensitive properties
                    unset(
                        $schema['owner'],
                        $schema['application'], 
                        $schema['organisation'],
                        $schema['authorization'],
                        $schema['deleted'],
                        $schema['configuration'],
                        $schema['source']
                    );
                    
                    $schemaObjects[] = $schema;
                }
            }
            $orderedCatalog['schemas'] = $schemaObjects;
        }

        // Add the search and directory urls
        $orderedCatalog['search']    = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute("opencatalogi.search.index"));
        $orderedCatalog['directory'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute("opencatalogi.directory.index"));
        $orderedCatalog['catalog']   = $orderedCatalog['id'];

        // TODO: This should be mapped to the stoplight documentation
        return $orderedCatalog;

    }//end getDirectoryFromCatalog()


    /**
     * Get all directories (listings and catalogi) in a unified format
     *
     * @return array Array containing directory results and total count
     * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function getDirectories(): array
    {
        $listings = [];
        $catalogi = [];

        // Get configuration values for catalog and listing schemas/registers
        $catalogSchema   = $this->config->getValueString($this->appName, 'catalog_schema', '');
        $catalogRegister = $this->config->getValueString($this->appName, 'catalog_register', '');
        $listingSchema   = $this->config->getValueString($this->appName, 'listing_schema', '');
        $listingRegister = $this->config->getValueString($this->appName, 'listing_register', '');

        // Build schema lookup array once for performance
        $schemaLookup = $this->buildSchemaLookup();

        // Get catalogs using configuration (same method as PublicationService)
        if (!empty($catalogSchema) && !empty($catalogRegister)) {
            try {
                $config = [];
                $config['filters']['register'] = $catalogRegister;
                $config['filters']['schema']   = $catalogSchema;
                //$config['extend'] = ['schemas'];
                
                $catalogsFromService = $this->getObjectService()->findAll($config);
                
                // Process the results like PublicationService does
                foreach ($catalogsFromService as $catalog) {
                    $catalogData = $catalog->jsonSerialize();
                    $catalogi[] = $this->getDirectoryFromCatalog($catalogData, $schemaLookup);
                }
            } catch (\Exception $e) {
                // Log error but continue
                \OC::$server->getLogger()->warning('DirectoryService: Failed to get catalogs: ' . $e->getMessage());
            }
        }

        // Get listings using configuration
        if (!empty($listingSchema) && !empty($listingRegister)) {
            try {
                $config = [];
                $config['filters']['register'] = $listingRegister;
                $config['filters']['schema']   = $listingSchema;
                
                $listingsFromService = $this->getObjectService()->findAll($config);
                
                foreach ($listingsFromService as $listing) {
                    $listingData = $listing->jsonSerialize();
                    $listings[] = $this->getDirectoryFromListing($listingData);
                }
            } catch (\Exception $e) {
                // Log error but continue
                \OC::$server->getLogger()->warning('DirectoryService: Failed to get listings: ' . $e->getMessage());
            }
        }

        // Fallback: if no configuration or no results, try to get all objects and filter manually
        if (empty($catalogi) && empty($listings)) {
            try {
                $allObjectsResult = $this->getObjectService()->findAll(['limit' => 100]);
                
                foreach (($allObjectsResult['results'] ?? []) as $object) {
                    $data = $object instanceof Entity === true ? $object->jsonSerialize() : $object;
                    
                    // Check if this is a catalog by schema slug
                    if (isset($data['@self']['schema']['slug']) && $data['@self']['schema']['slug'] === 'catalog') {
                        $catalogi[] = $this->getDirectoryFromCatalog($data, $schemaLookup);
                    }
                    // Check if this is a listing by schema slug  
                    elseif (isset($data['@self']['schema']['slug']) && $data['@self']['schema']['slug'] === 'listing') {
                        $listings[] = $this->getDirectoryFromListing($data);
                    }
                }
            } catch (\Exception $e) {
                // Log error and return empty arrays
                \OC::$server->getLogger()->error('DirectoryService: Failed to get any objects: ' . $e->getMessage());
            }
        }

        // Filter out the catalogi that are not listed
        $catalogi = array_filter(
            $catalogi,
            function ($catalog) {
                  return $catalog['listed'] !== false;
              }
        );

        // Remove the 'listed' property from all catalogi objects
        $catalogi = array_map(
            function ($catalog) {
                  unset($catalog['listed']);
                  return $catalog;
              },
            $catalogi
        );

        // Merge listings and catalogi into a new array
        $mergedDirectories = array_merge($listings, $catalogi);

        // Create a wrapper array with 'results' and 'total'
        $directories = [
            'results' => $mergedDirectories,
            'total'   => count($mergedDirectories),
        ];

        return $directories;

    }//end getDirectories()


    /**
     * Run a synchronisation based on cron
     *
     * @return array An array containing synchronization results
     * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     * @throws GuzzleException
     */
    public function doCronSync(): array
    {
        $results  = [];
        $listings = $this->getObjectService()->getObjects(objectType: 'listing');

        // Extract unique directory URLs
        // Get unique directories from listings
        $uniqueDirectories = array_unique(array_column($listings, 'directory'));

        // Add default OpenCatalogi directory if not already present
        $defaultDirectory = 'https://directory.opencatalogi.nl/apps/opencatalogi/api/directory';
        if (!in_array($defaultDirectory, $uniqueDirectories)) {
            $uniqueDirectories[] = $defaultDirectory;
        }

        // Sync each unique directory
        foreach ($uniqueDirectories as $directoryUrl) {
            try {
                $result = $this->syncExternalDirectory($directoryUrl);
            } catch (DirectoryUrlException $exception) {
                continue;
            }

            $results = array_merge_recursive($results, $result);
        }

        return $results;

    }//end doCronSync()


    /**
     * Validate an external listing.
     *
     * @param  array $listing The listing to validate
     * @return bool True if the listing is valid, false otherwise
     */
    public function validateExternalListing(array $listing): bool
    {
        if (empty($listing['catalog']) === true || Uuid::isValid($listing['catalog']) === false) {
            return false;
        }

        // TODO: Implement validation logic here
        return true;

    }//end validateExternalListing()


    /**
     * Update a listing
     *
     * @param array $newListing The new listing
     * @param array $oldListing The old listing
     *
     * @return array The updated listing
     * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     */
    public function updateListing(array $newListing, array $oldListing): array
    {
        // Let's see if these changed by checking them against the hash
        $newHash = hash('sha256', json_encode($newListing));
        $oldHash = hash('sha256', json_encode($oldListing));
        if ($newHash === $oldHash) {
            return $oldListing;
        }

        // Do not update version, because we copy the version from the source
        $newListing = $this->getObjectService()->saveObject('listing', $oldListing, updateVersion: false);

        return $newListing instanceof Entity === true ? $newListing->jsonSerialize() : $newListing;

    }//end updateListing()


    /**
     * Checks if the URL complies to basic rules.
     *
     * @param  string $url The url to check.
     * @return void
     * @throws DirectoryUrlException Thrown if the url is invalid.
     */
    private function checkConditions(string $url): void
    {
        if (empty($url) === true) {
            throw new DirectoryUrlException('URL is required');
        }

        // Check if URL contains the base url of this instance.
        if (str_contains(haystack: strtolower($url), needle: $this->urlGenerator->getBaseUrl()) === true) {
            throw new DirectoryUrlException('Cannot load current directory');
        }

        // Check if URL contains 'local' and throw exception if it does
        if (str_contains(strtolower($url), 'local') === true) {
            throw new DirectoryUrlException('Local urls are not allowed');
        }

        // Validate the URL
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new DirectoryUrlException('Invalid URL provided');
        }

    }//end checkConditions()


    /**
     * Synchronize with an external directory
     *
     * @param string $url The URL of the external directory
     *
     * @return array An array containing synchronization results
     * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface
     * @throws GuzzleException|\OCP\DB\Exception
     * @throws DirectoryUrlException
     */
    public function syncExternalDirectory(string $url): array
    {
        // Log successful broadcast
        \OC::$server->getLogger()->info('Synchronizing directory with '.$url);

        $this->checkConditions($url);

        try {
            $checkUrls[] = $url;
            // Get the directory data
            $result = $this->client->get($url);

            // Fallback to the /api/directory endpoint if the result is not JSON
            if (str_contains($result->getHeader('Content-Type')[0], 'application/json') === false) {
                $checkUrls[] = $url.'/index.php/apps/opencatalogi/api/directory';
                $url         = rtrim($url, '/').'/apps/opencatalogi/api/directory';
                $result      = $this->client->get($url);
                $checkUrls[] = $url;
            }
        } catch (ClientException | RequestException | ServerException $e) {
            // If we get a 404, the directory no longer exists
            if ($e->getResponse()->getStatusCode() === 404) {
                // Delete all listings for this directory since it no longer exists
                $this->deleteListingsByDirectory($url);
                throw new \Exception('Directory no longer exists at '.$url);
            }

            throw $e;
// Re-throw other client exceptions
        }//end try

        // Decode the result
        $newListings = json_decode($result->getBody()->getContents(), true)['results'];

        // Get all current listings for this directory
        $currentListings = $this->getObjectService()->getObjects(
            objectType: 'listing'
        );

        // Remove any listings without a catalog ID from the database
        foreach ($currentListings as $listing) {
            if (empty($listing['catalog'])) {
                // Delete the listing from the database
                $this->getObjectService()->deleteObject('listing', $listing['id']);
                // Remove from current listings array
                unset($currentListings[array_search($listing, $currentListings)]);
            }
        }

        // Index the filtered listings by catalog ID
        // array_column() with null as second parameter returns complete array entries
        // This will return complete listing objects indexed by their catalog ID
        $oldListings = array_column(
            $currentListings,
            null,
            // null returns complete array entries rather than a specific column
            'catalog'
            // Index by catalog ID
        );

        $oldListingDirectories = array_unique(array: array_column(array: $currentListings, column_key: 'directory'));

        // Initialize arrays to store results
        $addedListings         = [];
        $updatedListings       = [];
        $invalidListings       = [];
        $foundDirectories      = [];
        $removedListings       = [];
        $discoveredDirectories = [];

        // Process each new listing
        foreach ($newListings as $listing) {
            // Validate the listing (Note: at this point 'uuid' has been moved to the 'id' field in each $listing)
            if ($this->validateExternalListing($listing) === false) {
                $invalidListings[] = $listing['directory'].'/'.$listing['id'];
                continue;
            }

            if (in_array(needle: $listing['directory'], haystack: $checkUrls) === false
                && in_array(needle: $listing['directory'], haystack: $oldListingDirectories) === false
            ) {
                $discoveredDirectories[] = $listing['directory'];

                continue;
            } else if (in_array(needle: $listing['directory'], haystack: $checkUrls) === false) {
                continue;
            }

            // Check if we already have this listing by looking up its catalog ID in the oldListings array
            $oldListing = $oldListings[$listing['catalog']] ?? null;

            // If no existing listing found, prepare the new listing data
            if ($oldListing === null) {
                $listing['hash'] = hash('sha256', json_encode($listing));
                unset($listing['id']);
            } else {
                // Update existing listing
                $this->updateListing($listing, $oldListing);
                // @todo listing will be added to updatedList even if nothing changed...
                $updatedListings[] = $listing['directory'].'/'.$listing['id'];
                // unset the listing from the oldListings array
                unset($oldListings[$listing['id']]);
                continue;
            }

            // Save the new listing
            $listingObject = $this->getObjectService()->saveObject('listing', $listing);
            if ($listingObject instanceof Entity) {
                $listing = $listingObject->jsonSerialize();
            } else {
                $listing = $listingObject;
            }

            $foundDirectories[] = $listing['directory'];
            $addedListings[]    = $listing['directory'].'/'.$listing['id'];
        }//end foreach

        // Process each removed listing
        foreach ($oldListings as $oldListing) {
            $removedListings[] = $oldListing['directory'].'/'.$oldListing['id'];
            $this->getObjectService()->deleteObject('listing', $oldListing['id']);
        }

        // Lets inform our new friends that we exist
        foreach ($foundDirectories as $foundDirectory) {
            $this->broadcastService->broadcast($foundDirectory);
        }

        foreach ($discoveredDirectories as $discoveredDirectory) {
            $this->syncExternalDirectory($discoveredDirectory);
        }

        // Return the results
        return [
            'invalidListings' => $invalidListings,
            'addedListings'   => $addedListings,
            'updatedListings' => $updatedListings,
            'removedListings' => $removedListings,
            'total'           => (count($addedListings) + count($updatedListings)),
        ];

    }//end syncExternalDirectory()


    /**
     * Delete all listings belonging to a directory
     *
     * @param string $directoryUrl The directory URL to delete listings for
     * @return void
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function deleteListingsByDirectory(string $directoryUrl): void
    {
        // Get all current listings for this directory
        $currentListings = $this->getObjectService()->getObjects(
            objectType: 'listing',
            filters: ['directory' => $directoryUrl]
        );
        // Delete all listings
        foreach ($currentListings as $listing) {
            $this->getObjectService()->deleteObject('listing', $listing['id']);
        }

    }//end deleteListingsByDirectory()


    /**
     * Copy or update a publication type from an external URL
     *
     * @param  string $url The URL of the publication type to copy or update
     * @return array The copied or updated publication type
     * @throws GuzzleException
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \InvalidArgumentException If the URL is invalid
     */
    public function syncPublicationType(string $url): array
    {
        // Fetch the publication type data from the external URL
        try {
            $response = $this->client->get($url);
        } catch (GuzzleException $e) {
            throw new \InvalidArgumentException('Unable to fetch data from the provided URL: '.$e->getMessage());
        }

        $publicationType = json_decode($response->getBody()->getContents(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON data received from the URL');
        }

        // Set the source to the URL
        $publicationType['source'] = $url;

        // Prevent against malicious input
        unset($publicationType['id']);
        unset($publicationType['uuid']);

        // Check if a publication type with the same name already exists
        /*
            $existingPublicationType = $this->getObjectService()->getObjects(
            objectType: 'publicationType',
            limit: 1,
            filters: [
                ['source' => $url]
            ]
            );
        */

        // TODO: THis is a hacky workaround for failing filters: PRIORITY: High
        $existingPublicationTypes = $this->getObjectService()->getObjects(
            objectType: 'publicationType',
        );
        // Filter publication types to only include those with a matching source
        $existingPublicationTypes = array_filter(
            $existingPublicationTypes,
            function ($publicationType) use ($url) {
                  // Check if the publication type has a 'source' property and if it matches the given source
                  return isset($publicationType['source']) && $publicationType['source'] === $url;
              }
        );

        if (!empty($existingPublicationTypes)) {
            // Update existing publication types
            $updatedPublicationTypes = [];
            foreach ($existingPublicationTypes as $existingType) {
                $updatedType               = $this->getObjectService()->updateObject('publicationType', $existingType['id'], $publicationType);
                $updatedPublicationTypes[] = $updatedType->jsonSerialize();
            }

            return $updatedPublicationTypes;
        } else {
            // Save the new publication type
            $newPublicationType = $this->getObjectService()->saveObject('publicationType', $publicationType);
            return [$newPublicationType->jsonSerialize()];
        }

    }//end syncPublicationType()


    /**
     * Build a lookup array of schemas keyed by their ID for performance
     *
     * @return array Array of schemas keyed by schema ID
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function buildSchemaLookup(): array
    {
        $schemaLookup = [];
        
        try {
            // Get all registers which contain schemas
            $registers = $this->getObjectService()->getRegisters();
            
            // Loop through registers to extract schemas
            foreach ($registers as $register) {
                if (isset($register['schemas']) && is_array($register['schemas'])) {
                    foreach ($register['schemas'] as $schema) {
                        // Use the schema ID directly (not from @self property)
                        if (isset($schema['id'])) {
                            $schemaLookup[$schema['id']] = $schema;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            \OC::$server->getLogger()->warning('DirectoryService: Failed to build schema lookup: ' . $e->getMessage());
        }
        
        return $schemaLookup;
    }//end buildSchemaLookup()

}//end class
