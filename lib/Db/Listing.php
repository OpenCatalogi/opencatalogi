<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Listing extends Entity implements JsonSerializable
{

	protected ?string $title 	   = null;
	protected ?string $reference   = null;
	protected ?string $summary     = null;
	protected ?string $description = null;
	protected ?string $search	   = null;
	protected ?string $directory   = null;
	protected ?string $metadata    = null;
	protected ?string $status	   = null;
	protected ?DateTime $lastSync    = null;
	protected bool    $default	   = false;
	protected bool    $available   = false;

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'search', type: 'string');
		$this->addType(fieldName: 'directory', type: 'string');
		$this->addType(fieldName: 'metadata', type: 'string');
		$this->addType(fieldName: 'status', type: 'string');
		$this->addType(fieldName: 'lastSync', type: 'datetime');
		$this->addType(fieldName: 'default', type: 'boolean');
		$this->addType(fieldName: 'available', type: 'boolean');
	}

	public function hydrate(array $object): self
	{
		foreach($object as $key => $value) {
			$method = 'set'.ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
				var_dump("Erroring writing $key");
			}
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		return [
			'id' 		  => $this->id,
			'title' 	  => $this->title,
			'summary' 	  => $this->summary,
			'description' => $this->description,
			'search' 	  => $this->search,
			'directory'	  => $this->search,
			'metadata'	  => $this->search,
			'status' 	  => $this->search,
			'lastSync' 	  => $this->search,
			'default' 	  => $this->search,
			'available'   => $this->search,
		];
	}
}
