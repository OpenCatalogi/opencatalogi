<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * Class Page
 * Represents a page entity in the OpenCatalogi application
 * 
 * @package OCA\OpenCatalogi\Db
 */
class Page extends Entity implements JsonSerializable
{
	// Basic page properties
	protected ?string $uuid = null;
	protected ?string $version = null;
	protected ?string $name = null;
	protected ?string $slug = null;
	protected ?array $contents = null;
	protected ?DateTime $created = null;
	protected ?DateTime $updated = null;

	/**
	 * Constructor to initialize field types
	 */
	public function __construct() {
		$this->addType(fieldName: 'uuid', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'name', type: 'string');
		$this->addType(fieldName: 'slug', type: 'string');
		$this->addType(fieldName: 'contents', type: 'json');
		$this->addType(fieldName: 'created', type: 'datetime');
		$this->addType(fieldName: 'updated', type: 'datetime');
	}

	/**
	 * Get array of JSON field names
	 * 
	 * @return array List of field names that are JSON type
	 */
	public function getJsonFields(): array
	{
		return array_keys(
			array_filter($this->getFieldTypes(), function ($field) {
				return $field === 'json';
			})
		);
	}

	/**
	 * Hydrate the entity from an array of data
	 * 
	 * @param array $object Data to hydrate from
	 * @return self
	 */
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
			}
		}

		return $this;
	}

	/**
	 * Serialize the entity to JSON
	 * 
	 * @return array Serialized data
	 */
	public function jsonSerialize(): array
	{
		$array = [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'version' => $this->version,
			'name' => $this->name,
			'slug' => $this->slug,
			'contents' => $this->contents,
			'created_at' => $this->created?->format('c'),
			'updated_at' => $this->updated?->format('c')
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
