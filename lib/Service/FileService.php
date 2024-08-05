<?php

namespace OCA\OpenCatalogi\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OCP\IAppConfig;
use OCP\IUserSession;
use Psr\Log\LoggerInterface;

class FileService
{
	protected string $appName = 'opencatalogi';
	private Client $client;

	public function __construct(
		private readonly IUserSession $userSession,
		private readonly LoggerInterface $logger,
		private readonly IAppConfig $config
	) {
		$this->client = new Client();
	}

	/**
	 * Sets the appName used for getting configuration, this should be set after creating this service!
	 *
	 * @param string $appName The appName to set.
	 * @return void
	 */
	public function setAppName(string $appName): void
	{
		$this->appName = $appName;
	}

	/**
	 * Gets and returns the current host / domain with correct protocol.
	 *
	 * @return string The current http/https domain url.
	 */
	private function getCurrentDomain(): string
	{
		// Check if the request is over HTTPS
		$isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
		$protocol = $isHttps ? 'https://' : 'http://';

		// Get the host (domain)
		$host = $_SERVER['HTTP_HOST'];

		// Construct the full URL
		return $protocol . $host;
	}

	/**
	 * Creates and returns a share link for a file (or folder).
	 * (https://docs.nextcloud.com/server/latest/developer_manual/client_apis/OCS/ocs-share-api.html#create-a-new-share)
	 *
	 * @param string $path Path to the file/folder which should be shared.
	 * @param int|null $shareType 0 = user; 1 = group; 3 = public link; 4 = email; 6 = federated cloud share; 7 = circle; 10 = Talk conversation
	 * @param int|null $permissions 1 = read; 2 = update; 4 = create; 8 = delete; 16 = share; 31 = all (default: 31, for public shares: 1)
	 *
	 * @return string The share link.
	 * @throws GuzzleException|Exception In case the Guzzle call returns an exception.
	 */
	public function createShareLink(string $path, ?int $shareType = 3, ?int $permissions = null): string
	{
		// API endpoint to create a share
		$url = "{$this->getCurrentDomain()}/ocs/v2.php/apps/files_sharing/api/v1/shares";

		// Get the admin username & password for auth
		$username = $this->config->getValueString(app: $this->appName, key: 'adminUsername', default: 'admin');
		$password = $this->config->getValueString(app: $this->appName, key: 'adminPassword', default: 'admin');

		// Get the current username
		$currentUser = $this->userSession->getUser();
		$currentUsername = $currentUser ? $currentUser->getUID() : 'Guest';

		// Data for the POST request
		$options = [
			'auth' => [$username, $password],
			'headers' => [
				'OCS-APIREQUEST' => 'true',
				'Content-Type' => 'application/x-www-form-urlencoded'
			],
			'form_params' => [
				'path' => $path,
				'shareType' => $shareType,
				'permissions' => $permissions,
				'shareWith' => $currentUsername
			]
		];

		try {
			$response = $this->client->post(uri: $url, options: $options);
			$data = json_decode($response->getBody()->getContents(), true);
			return $data['ocs']['data']['url'] ?? '';
		} catch (Exception $e) {
			$this->logger->error('Failed to create share link: ' . $e->getMessage());
			throw $e;
		}
	}

}
