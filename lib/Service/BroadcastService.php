<?php

namespace OCA\OpenCatalogi\Service;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Uid\Uuid;
use OCA\OpenCatalogi\Service\ObjectService;

/**
 * Service for broadcasting this OpenCatalogi directory to other instances.
 *
 * Provides functionality to notify external instances about this directory
 * through HTTP POST requests, either to a specific URL or to all known directories.
 */
class BroadcastService
{
	/** @var string The name of the app */
	private string $appName = 'opencatalogi';

	/** @var Client The HTTP client for making requests */
	private Client $client;

	/**
	 * Constructor for BroadcastService
	 *
	 * @param IURLGenerator $urlGenerator URL generator interface
	 * @param IAppConfig $config App configuration interface
	 * @param ObjectService $objectService Object service for handling objects
	 */
	public function __construct(
		private readonly IURLGenerator $urlGenerator,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService,
	)
	{
		$this->client = new Client([]);
	}

	/**
	 * Broadcast this OpenCatalogi directory to one or more instances
	 *
	 * @param string|null $url Optional URL of a specific instance to broadcast to
	 * @throws DoesNotExistException|MultipleObjectsReturnedException|ContainerExceptionInterface|NotFoundExceptionInterface|GuzzleException
	 */
	public function broadcast(?string $url = null): void {
		// Initialize hooks array
		$hooks = [];

		// If URL is provided, add it to hooks
		if ($url !== null) {
			$hooks[] = $url;
		}
		// Otherwise get all unique directory URLs
		else {
			$listings = $this->objectService->getObjects(objectType: 'listing');
			$hooks = array_unique(array_column($listings, 'directory'));
		}

		// Get the URL of this directory
		$directoryUrl = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.directory.index'));

		// Broadcast to each hook
		foreach ($hooks as $hook) {
			// Send POST request with directory URL
			try {
				$this->client->post($hook, [
					'json' => [
						'directory' => $directoryUrl
					]
				]);

				// Log successful broadcast
				\OC::$server->getLogger()->info('Successfully broadcasted to ' . $hook);

			} catch (\Exception $e) {
				// Throw a warning since broadcasting failure shouldn't break the application flow
				// but we still want to notify about the issue
				\OC::$server->getLogger()->warning('Failed to broadcast to ' . $hook . ': ' . $e->getMessage());
			}
		}
	}
}
