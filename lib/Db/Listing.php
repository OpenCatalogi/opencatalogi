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
	protected ?string $catalogId   = null;
	protected ?string $status	   = null;
	protected ?DateTime $lastSync  = null;
	protected ?bool    $default	   = false;
	protected ?bool    $available  = false;

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'search', type: 'string');
		$this->addType(fieldName: 'directory', type: 'string');
		$this->addType(fieldName: 'metadata', type: 'string');
		$this->addType(fieldName: 'catalogId', type: 'string');
		$this->addType(fieldName: 'status', type: 'string');
		$this->addType(fieldName: 'lastSync', type: 'datetime');
		$this->addType(fieldName: 'default', type: 'boolean');
		$this->addType(fieldName: 'available', type: 'boolean');
	}

	public function getJsonFields(): array
	{
		return array_keys(
			array_filter($this->getFieldTypes(), function ($field) {
				return $field === 'json';
			})
		);
	}

	public function hydrate(array $object): self
	{
		$jsonFields = $this->getJsonFields();

		foreach($object as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === []) {
				$value = null;
			}

			$method = 'set'.ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
//				var_dump("Error writing $key");
			}
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		$array = [
			'id' 		  => $this->id,
			'title' 	  => $this->title,
			'summary' 	  => $this->summary,
			'description' => $this->description,
			'search' 	  => $this->search,
			'directory'	  => $this->directory,
			'metadata'	  => $this->metadata,
			'catalogId'	  => $this->catalogId,
			'status' 	  => $this->status,
			'lastSync' 	  => $this->lastSync,
			'default' 	  => $this->default,
			'available'   => $this->available,
		];

		$jsonFields = $this->getJsonFields();

		foreach ($array as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === null) {
				$array[$key] = [];
			}
		}

		return $array;
	}
}
