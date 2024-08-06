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
	 * Gets and returns an array with information about the current user.
	 * TODO: Username and password used for auth are currently set in config, this should (/could) be dynamic.
	 *
	 * @return array An array containing 'username', 'password' for auth and the 'currentUsername'.
	 */
	private function getUserInfo(): array
	{
		// Get the current user
		$currentUser = $this->userSession->getUser();

		return [
			'username' => $this->config->getValueString(app: $this->appName, key: 'adminUsername', default: 'admin'),
			'password' => $this->config->getValueString(app: $this->appName, key: 'adminPassword', default: 'admin'),
			'currentUsername' => $currentUser ? $currentUser->getUID() : 'Guest'
		];
	}

	/**
	 * Creates and returns a share link for a file (or folder).
	 * (https://docs.nextcloud.com/server/latest/developer_manual/client_apis/OCS/ocs-share-api.html#create-a-new-share)
	 *
	 * @param string $path Path (from root) to the file/folder which should be shared.
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

		// Get the admin username & password for auth & get the current username
		$userInfo = $this->getUserInfo();

		// Data for the POST request
		$options = [
			'auth' => [$userInfo['username'], $userInfo['password']],
			'headers' => [
				'OCS-APIREQUEST' => 'true',
				'Content-Type' => 'application/x-www-form-urlencoded'
			],
			'form_params' => [
				'path' => $path,
				'shareType' => $shareType,
				'permissions' => $permissions,
				'shareWith' => $userInfo['currentUsername']
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

	/**
	 * Uploads a file to NextCloud. Will overwrite a file if it already exists and create a new one if it doesn't exist.
	 *
	 * @param mixed $content The content of the file.
	 * @param string|null $filePath Path (from root) where to save the file. NOTE: this should include the name and extension/format of the file as well! (example.pdf)
	 * @param bool|null $update If set to true, the response status code 204 will also be seen as a success result. (NextCloud will return 204 when successfully updating a file)
	 *
	 * @return bool True if successful.
	 * @throws GuzzleException|Exception In case the Guzzle call returns an exception.
	 */
	public function uploadFile(mixed $content, ?string $filePath = '', ?bool $update = false): bool
	{
		// Get the admin username & password for auth & get the current username
		$userInfo = $this->getUserInfo();

		// API endpoint to upload the file
		$url = $this->getCurrentDomain() . '/remote.php/dav/files/'
			. $userInfo['currentUsername'] . '/' . trim(string: $filePath, characters: '/');

		try {
			$response = $this->client->request(method: 'PUT', uri: $url, options: [
				'auth' => [$userInfo['username'], $userInfo['password']],
				'body' => $content
			]);

			// 201 Created indicates that the file was created, 204 No Content indicates that the file was updated.
			if ($response->getStatusCode() === 201 || ($update === true && $response->getStatusCode() === 204)) {
				return true;
			}
		} catch (Exception $e) {
			$str = $update === true ? 'update' : 'upload';
			$this->logger->error("File $str failed: " . $e->getMessage());
			throw $e;
		}

		return false;
	}

	/**
	 * Deletes a file from NextCloud.
	 *
	 * @param string $filePath Path (from root) to the file you want to delete.
	 *
	 * @return bool True if successful.
	 * @throws GuzzleException|Exception In case the Guzzle call returns an exception.
	 */
	public function deleteFile(string $filePath): bool
	{
		// Get the admin username & password for auth & get the current username
		$userInfo = $this->getUserInfo();

		// API endpoint to delete the file
		$url = $this->getCurrentDomain() . '/remote.php/dav/files/'
			. $userInfo['currentUsername'] . '/' . trim(string: $filePath, characters: '/');

		try {
			$response = $this->client->request(method: 'DELETE', uri: $url, options: [
				'auth' => [$userInfo['username'], $userInfo['password']],
			]);

			if ($response->getStatusCode() === 204) { // 204 No Content indicates the file was deleted.
				return true;
			}
		} catch (Exception $e) {
			$this->logger->error('File deletion failed: ' . $e->getMessage());
			throw $e;
		}

		return false;
	}

	/**
	 * Checks if a folder exists in NextCloud.
	 *
	 * @param string $folderPath Path (from root) to a folder you want to check if exists.
	 *
	 * @return bool True if the folder exists.
	 * @throws GuzzleException|Exception In case the Guzzle call returns an exception.
	 */
	public function folderExists(string $folderPath): bool
	{
		// Get the admin username & password for auth & get the current username
		$userInfo = $this->getUserInfo();

		// API endpoint to check if a folder exists
		$url = $this->getCurrentDomain() . '/remote.php/dav/files/'
			. $userInfo['currentUsername'] . '/' . trim(string: $folderPath, characters: '/');

		try {
			$response = $this->client->request(method: 'PROPFIND', uri: $url, options: [
				'auth' => [$userInfo['username'], $userInfo['password']],
				'headers' => [
					'Depth' => '1',
				],
				'body' => '<?xml version="1.0" encoding="UTF-8"?><d:propfind xmlns:d="DAV:"><d:prop><d:resourcetype/></d:prop></d:propfind>',
			]);

			return $response->getStatusCode() === 207; // Multi-Status indicates the folder exists.
		} catch (Exception $e) {
			if ($e->getCode() === 404) {
				return false;
			}

			$this->logger->error('Folder existence check failed: ' . $e->getMessage());
			throw $e;
		}
	}

	/**
	 * Creates a new folder in NextCloud, unless it already exists.
	 *
	 * @param string $folderPath Path (from root) to where you want to create a folder. NOTE: this should include the name of the folder as well! (/Media/exampleFolder)
	 *
	 * @return bool True if successfully created a new folder.
	 * @throws GuzzleException|Exception In case the Guzzle call returns an exception.
	 */
	public function createFolder(string $folderPath): bool
	{
		if ($this->folderExists(folderPath: $folderPath) === true) {
			$this->logger->info('Folder creation failed: Folder already exists');
			return false;
		}

		// Get the admin username & password for auth & get the current username
		$userInfo = $this->getUserInfo();

		// API endpoint to create a folder
		$url = $this->getCurrentDomain() . '/remote.php/dav/files/'
			. $userInfo['currentUsername'] . '/' . trim(string: $folderPath, characters: '/');

		try {
			$response = $this->client->request(method: 'MKCOL', uri: $url, options: [
				'auth' => [$userInfo['username'], $userInfo['password']],
			]);

			return $response->getStatusCode() === 201; // 201 Created indicates the folder was created.
		} catch (\Exception $e) {
			$this->logger->error('Folder creation failed: ' . $e->getMessage());
			throw $e;
		}
	}

}
