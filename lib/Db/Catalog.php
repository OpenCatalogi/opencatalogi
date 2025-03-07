<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Catalog extends Entity implements JsonSerializable
{
	protected ?string $uuid = null;
	protected ?string $uri = null;
	protected ?string $version = '0.0.1';
	protected ?string $title = null;
	protected ?string $summary = null;
	protected ?string $description = null;
	protected ?string $image = null;
	protected ?string $search = null;
	protected bool $listed = false;
	protected ?array $publicationTypes = null;
	protected ?string $organization = null;
	protected ?DateTime $updated = null;
	protected ?DateTime $created = null;

	public function __construct() {
		$this->addType(fieldName: 'uuid', type: 'string');
		$this->addType(fieldName: 'uri', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'image', type: 'string');
		$this->addType(fieldName: 'search', type: 'string');
		$this->addType(fieldName: 'listed', type: 'boolean');
		$this->addType(fieldName: 'publicationTypes', type: 'json');
		$this->addType(fieldName: 'organization', type: 'string');
		$this->addType(fieldName: 'updated', type: 'datetime');
		$this->addType(fieldName: 'created', type: 'datetime');
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

		// Remove any fields that start with an underscore
		// These are typically internal fields that shouldn't be updated directly
		foreach ($object as $key => $value) {
			if (str_starts_with($key, '_')) {
				unset($object[$key]);
			}
		}

		foreach ($object as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === []) {
				$value = null;
			}

			$method = 'set'.ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
				// Handle or log the exception as needed
			}
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		$array = [
			'id' => $this->id,
			'uri' => $this->uri,
			'uuid' => $this->uuid,
			'version' => $this->version,
			'title' => $this->title,
			'summary' => $this->summary,
			'description' => $this->description,
			'image' => $this->image,
			'search' => $this->search,
			'listed' => $this->listed,
			'publicationTypes' => $this->publicationTypes,
			'organization' => $this->organization,
			'updated' => $this->updated?->format('c'),
			'created' => $this->created?->format('c'),
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
