<?php

namespace OCA\OpenCatalogi\Service;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OCP\Files\IRootFolder;
use OCP\IAppConfig;
use OCP\IUserSession;
use OCP\Share\IShare;
use Psr\Log\LoggerInterface;

class FileService
{
	protected string $appName = 'opencatalogi';
	private Client $client;

	public function __construct(
		private readonly IUserSession $userSession,
		private readonly LoggerInterface $logger,
		private readonly IAppConfig $config,
		private readonly IRootFolder $rootFolder,
		private readonly IShare $share
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
	 * @param string $filePath Path (from root) where to save the file. NOTE: this should include the name and extension/format of the file as well! (example.pdf)
	 *
	 * @return bool True if successful.
	 * @throws Exception In case we can't write to file because it is not permitted.
	 */
	public function uploadFile(mixed $content, string $filePath): bool
	{
		$filePath = trim(string: $filePath, characters: '/');

		// Get the current user.
		$currentUser = $this->userSession->getUser();
		$userFolder = $this->rootFolder->getUserFolder(userId: $currentUser ? $currentUser->getUID() : 'Guest');

		// Check if file exists and create it if not.
		try {
			try {
				$userFolder->get(path: $filePath);
			} catch(\OCP\Files\NotFoundException $e) {
				$userFolder->newFile(path: $filePath);
				$file = $userFolder->get(path: $filePath);

				$file->putContent(data: $content);

				return true;
			}

			// File already exists.
			$this->logger->warning("File $filePath already exists.");
			return false;

		} catch(\OCP\Files\NotPermittedException $e) {
			$this->logger->error("Can't create file $filePath: " . $e->getMessage());
			throw new Exception('Can\'t write to file');
		}
	}

	/**
	 * Deletes a file from NextCloud.
	 *
	 * @param string $filePath Path (from root) to the file you want to delete.
	 *
	 * @return bool True if successful.
	 * @throws Exception In case deleting the file is not permitted.
	 */
	public function deleteFile(string $filePath): bool
	{
		$filePath = trim(string: $filePath, characters: '/');

		// Get the current user.
		$currentUser = $this->userSession->getUser();
		$userFolder = $this->rootFolder->getUserFolder(userId: $currentUser ? $currentUser->getUID() : 'Guest');

		// Check if file exists and delete it if it does.
		try {
			try {
				$file = $userFolder->get(path: $filePath);
				$file->delete();

				return true;
			} catch(\OCP\Files\NotFoundException $e) {
				// File does not exist.
				$this->logger->warning("File $filePath does not exist.");

				return false;
			}
		} catch(\OCP\Files\NotPermittedException $e) {
			$this->logger->error("Can't delete file $filePath: " . $e->getMessage());
			throw new Exception('Can\'t delete file');
		}
	}

	/**
	 * Creates a new folder in NextCloud, unless it already exists.
	 *
	 * @param string $folderPath Path (from root) to where you want to create a folder, include the name of the folder. (/Media/exampleFolder)
	 *
	 * @return bool True if successfully created a new folder.
	 * @throws Exception In case we can't create the folder because it is not permitted.
	 */
	public function createFolder(string $folderPath): bool
	{
		$folderPath = trim(string: $folderPath, characters: '/');

		// Get the current user.
		$currentUser = $this->userSession->getUser();
		$userFolder = $this->rootFolder->getUserFolder(userId: $currentUser ? $currentUser->getUID() : 'Guest');

		// Check if folder exists and if not create it.
		try {
			try {
				$userFolder->get(path: $folderPath);
			} catch(\OCP\Files\NotFoundException $e) {
				$userFolder->newFolder(path: $folderPath);

				return true;
			}

			// Folder already exists.
			$this->logger->info("This folder already exits $folderPath");
			return false;

		} catch(\OCP\Files\NotPermittedException $e) {
			$this->logger->error("Can't create folder $folderPath: " . $e->getMessage());
			throw new Exception('Can\'t create folder');
		}
	}

}
