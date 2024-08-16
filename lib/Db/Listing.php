<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Listing extends Entity implements JsonSerializable
{

	protected ?string   $title 	   = null;
	protected ?string   $reference   = null;
	protected ?string   $summary     = null;
	protected ?string   $description = null;
	protected ?string   $search	   = null;
	protected ?string   $directory   = null;
	protected ?array $metadata     = null;
	protected ?string   $catalogId   = null;
	protected ?string   $status	   = null;
	protected ?int      $statusCode  = null;
	protected ?DateTime $lastSync  = null;
	protected ?bool     $default	   = false;
	protected ?bool     $available  = false;
	protected ?string   $organisation = null;

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'search', type: 'string');
		$this->addType(fieldName: 'directory', type: 'string');
		$this->addType(fieldName: 'metadata', type: 'json');
		$this->addType(fieldName: 'catalogId', type: 'string');
		$this->addType(fieldName: 'status', type: 'string');
		$this->addType(fieldName: 'statusCode', type: 'integer');
		$this->addType(fieldName: 'lastSync', type: 'datetime');
		$this->addType(fieldName: 'default', type: 'boolean');
		$this->addType(fieldName: 'available', type: 'boolean');
		$this->addType(fieldName: 'organisation', type: 'string');
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

		if(isset($object['metadata']) === false) {
			$object['metadata'] = [];
		}

		foreach($object as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === []) {
				$value = null;
			}

			$method = 'set'.ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
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
			'lastSync' 	  => $this->lastSync?->format('c'),
			'default' 	  => $this->default,
			'available'   => $this->available,
			'organisation'=> json_decode($this->organisation, true),
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
