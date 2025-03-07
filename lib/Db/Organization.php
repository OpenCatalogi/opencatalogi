<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Organization extends Entity implements JsonSerializable
{
	protected ?string $uuid = null;
	protected ?string $uri = null;
	protected ?string $version = '0.0.1';
	protected ?string $title = null;
	protected ?string $summary = null;
	protected ?string $description = null;
	protected ?string $image = null;
	protected ?string $oin = null;
	protected ?string $tooi = null;
	protected ?string $rsin = null;
	protected ?string $pki = null;
	protected ?DateTime $upd = null;
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
		$this->addType(fieldName: 'oin', type: 'string');
		$this->addType(fieldName: 'tooi', type: 'string');
		$this->addType(fieldName: 'rsin', type: 'string');
		$this->addType(fieldName: 'pki', type: 'string');
		$this->addType(fieldName: 'upd', type: 'datetime');
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
			} catch (\Exception $exception) {}
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		$array = [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'uri' => $this->uri,
			'version' => $this->version,
			'title' => $this->title,
			'summary' => $this->summary,
			'description' => $this->description,
			'image' => $this->image,
			'oin' => $this->oin,
			'tooi' => $this->tooi,
			'rsin' => $this->rsin,
			'pki' => $this->pki,
			'upd' => $this->upd,
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
