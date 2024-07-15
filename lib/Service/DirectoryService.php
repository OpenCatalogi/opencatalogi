<?php

namespace OCA\opencatalog\lib\Service;

use DateTime;

class DirectoryService
{
	private function getDirectoryEntry(string $search): array
	{
		$now = new DateTime();
		return [
			'title' => '',
			'summary' => '',
			'description' => '',
			'search'	=> $search,
			'metadata'	=> '',
			'status'	=> '',
			'lastSync'	=> $now->format('c'),
			'default'	=> true,
		]
	}

	public function registerToExternalDirectory (string $search): int
	{

	}

	public function fetchFromExternalDirectory(): array
	{

	}

	public function updateToExternalDirectory(): array
	{

	}
}
