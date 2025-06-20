<?php
/**
 * Service for broadcasting this OpenCatalogi directory to other instances.
 *
 * Provides functionality to notify external instances about this directory
 * through HTTP POST requests, either to a specific URL or to all known directories.
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
use GuzzleHttp\Exception\GuzzleException;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Uid\Uuid;

/**
 * BroadcastService Class
 *
 * This class provides functionality to broadcast this OpenCatalogi directory to other instances.
 * It allows for broadcasting to a specific URL or to all known directories.
 */
class BroadcastService
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
     * Constructor for BroadcastService
     *
     * @param IURLGenerator      $urlGenerator URL generator interface
     * @param IAppConfig         $config       App configuration interface
     * @param ContainerInterface $container    Server container for dependency injection
     * @param IAppManager        $appManager   App manager for checking installed apps
     */
    public function __construct(
        private readonly IURLGenerator $urlGenerator,
        private readonly IAppConfig $config,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager,
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
     * Broadcast this OpenCatalogi directory to one or more instances
     *
     * @param  string|null $url Optional URL of a specific instance to broadcast to
     * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface|GuzzleException
     */
    public function broadcast(?string $url=null): void
    {
        // Initialize hooks array
        $hooks = [];

        // If URL is provided, add it to hooks
        if ($url !== null) {
            $hooks[] = $url;
        }
        // Otherwise get all unique directory URLs
        else {
            $listings = $this->getObjectService()->getObjects(objectType: 'listing');
            $hooks    = array_unique(array_column($listings, 'directory'));
        }

        // Get the URL of this directory
        $directoryUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.directory.index'));

        // Broadcast to each hook
        foreach ($hooks as $hook) {
            // Send POST request with directory URL
            try {
                $this->client->post(
                    $hook,
                    [
                        'json' => ['directory' => $directoryUrl],
                    ]
                );

                // Log successful broadcast
                \OC::$server->getLogger()->info('Successfully broadcasted to '.$hook);
            } catch (\Exception $e) {
                // Throw a warning since broadcasting failure shouldn't break the application flow
                // but we still want to notify about the issue
                \OC::$server->getLogger()->warning('Failed to broadcast to '.$hook.': '.$e->getMessage());
            }
        }

    }//end broadcast()


}//end class
