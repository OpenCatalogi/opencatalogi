<?php

namespace OCA\OpenCatalogi\Service;

use DateTime;
use Exception;
use OCP\Files\IRootFolder;
use OCP\IUserSession;
use OCP\Share\IManager;
use Psr\Log\LoggerInterface;

class FileService
{
	public function __construct(
		private readonly IUserSession $userSession,
		private readonly LoggerInterface $logger,
		private readonly IRootFolder $rootFolder,
		private readonly IManager $shareManager
	) {}

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
	 * @param string $path Path (from root) to the file/folder which should be shared.
	 * @param int|null $shareType 0 = user; 1 = group; 3 = public link; 4 = email; 6 = federated cloud share; 7 = circle; 10 = Talk conversation
	 * @param int|null $permissions 1 = read; 2 = update; 4 = create; 8 = delete; 16 = share; 31 = all (default: 31, for public shares: 1)
	 *
	 * @return string The share link.
	 * @throws Exception In case the Guzzle call returns an exception.
	 */
	public function createShareLink(string $path, ?int $shareType = 3, ?int $permissions = null): string
	{
		if ($permissions === null) {
			$permissions = 31;
			if ($shareType === 3) {
				$permissions = 1;
			}
		}

		$path = trim(string: $path, characters: '/');

		// Get the current user.
		$currentUser = $this->userSession->getUser();
		$userId = $currentUser ? $currentUser->getUID() : 'Guest';
		try {
			$userFolder = $this->rootFolder->getUserFolder(userId: $userId);
		} catch(\OCP\Files\NotPermittedException) {
			$this->logger->error("Can't create share link for $path because user (folder) couldn't be found");

			return "User (folder) couldn't be found";
		}

		try {
			// Note: if we ever want to create share links for folders instead of files, just remove this try catch and only use setTarget, not setNodeId.
			$file = $userFolder->get(path: $path);
		} catch(\OCP\Files\NotFoundException $e) {
			$this->logger->error("Can't create share link for $path because file doesn't exist");

			return 'File not found at '.$path;
		}

		$share = $this->shareManager->newShare();
		$share->setTarget(target: "/$path");
		$share->setNodeId(fileId: $file->getId());
		$share->setNodeType(type: 'file');
		$share->setShareType(shareType: $shareType);
		if ($permissions !== null) {
			$share->setPermissions(permissions: $permissions);
		}
		$share->setSharedBy(sharedBy: $userId);
		$share->setShareOwner(shareOwner: $userId);
		$share->setShareTime(shareTime: new DateTime());
		$share->setStatus(status: $share::STATUS_ACCEPTED);

		try
		{
			$share = $this->shareManager->createShare(share: $share);
			return $this->getCurrentDomain() . '/index.php/s/' . $share->getToken();
		} catch (Exception $exception) {
			$this->logger->error("Can't create share link for $path: " . $exception->getMessage());

			throw new Exception('Can\'t create share link');
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
