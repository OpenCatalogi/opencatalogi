<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Listing extends Entity implements JsonSerializable
{
	protected ?string   $uuid             = null;
	protected ?string   $version          = '0.0.1';
	protected ?string   $title            = null;
	protected ?string   $summary          = null;
	protected ?string   $description      = null;
	protected ?string   $search           = null;
	protected ?string   $directory        = null;
	protected ?array   $organization     = null;
	protected ?array    $publicationTypes = null;
	protected ?string   $status           = null;
	protected ?DateTime $lastSync         = null;
	protected ?bool     $default          = false;
	protected ?bool     $available        = false;
	protected ?string   $catalog        = null;
	protected ?string   $hash        = null;
	protected ?int      $statusCode       = null;
	protected ?DateTime $updated          = null;
	protected ?DateTime $created          = null;

	public function __construct() {
		$this->addType(fieldName: 'uuid', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'search', type: 'string');
		$this->addType(fieldName: 'directory', type: 'string');
		$this->addType(fieldName: 'organization', type: 'json');
		$this->addType(fieldName: 'publicationTypes', type: 'json');
		$this->addType(fieldName: 'status', type: 'string');
		$this->addType(fieldName: 'lastSync', type: 'datetime');
		$this->addType(fieldName: 'default', type: 'boolean');
		$this->addType(fieldName: 'available', type: 'boolean');
		$this->addType(fieldName: 'catalog', type: 'string');
		$this->addType(fieldName: 'hash', type: 'string');
		$this->addType(fieldName: 'statusCode', type: 'integer');
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

		if (isset($object['publicationTypes']) === false) {
			$object['publicationTypes'] = [];
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

	public function jsonSerialize(): array
	{
		$array = [
			'id'               => $this->id,
			'uuid'             => $this->uuid,
			'version'          => $this->version,
			'title'            => $this->title,
			'summary'          => $this->summary,
			'description'      => $this->description,
			'search'           => $this->search,
			'directory'        => $this->directory,
			'organization'     => $this->organization,
			'publicationTypes' => $this->publicationTypes,
			'status'           => $this->status,
			'lastSync'         => $this->lastSync?->format('c'),
			'default'          => $this->default,
			'available'        => $this->available,
			'catalog'          => $this->catalog,
			'hash'             => $this->hash,
			'statusCode'       => $this->statusCode,
			'updated'          => $this->updated?->format('c'),
			'created'          => $this->created?->format('c'),
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
