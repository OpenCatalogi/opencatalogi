<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Attachment extends Entity implements JsonSerializable
{

	protected ?string $reference    = null;
	protected ?string $title        = null;
	protected ?string $summary      = null;
	protected ?string $description  = null;
	protected ?array $labels        = null;
	protected ?string $accessUrl    = null;
	protected ?string $downloadUrl  = null;
	protected ?string $type         = null;
	protected ?string $extension    = null;
	protected ?int $size			= null;
	protected ?array $anonymization = null;
	protected ?array $language      = null;
	protected ?string $versionOf	= null;
	protected ?string $hash			= null;
	protected ?DateTime $published  = null;
	protected ?DateTime $modified	= null;
	protected ?string $license		= null;

	public function __construct() {
		$this->addType(fieldName: 'reference', type: 'string');
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'labels', type: 'json');
		$this->addType(fieldName: 'accessUrl', type: 'string');
		$this->addType(fieldName: 'downloadUrl', type: 'string');
		$this->addType(fieldName: 'type', type: 'string');
		$this->addType(fieldName: 'extension', type: 'string');
		$this->addType(fieldName: 'size', type: 'int');
		$this->addType(fieldName: 'versionOf', type: 'string');
		$this->addType(fieldName: 'hash', type: 'string');
		$this->addType(fieldName: 'anonymization', type: 'json');
		$this->addType(fieldName: 'language', type: 'json');
		$this->addType(fieldName: 'modified', type: 'datetime');
		$this->addType(fieldName: 'published', type: 'datetime');
		$this->addType(fieldName: 'license', type: 'string');

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
			'id' => $this->id,
			'title' => $this->title,
			'reference' => $this->reference,
			'summary' => $this->summary,
			'description' => $this->description,
			'labels' => $this->labels,
			'accessUrl' => $this->accessUrl,
			'downloadUrl' => $this->downloadUrl,
			'type' => $this->type,
			'extension' => $this->extension,
			'size' => $this->size,
			'versionOf' => $this->versionOf,
			'hash'	=> $this->hash,
			'anonymization' => $this->anonymization,
			'language' => $this->language,
			'modified'	=> $this->modified?->format('c'),
			'published' => $this->published?->format('c'),
			'license' => $this->license,
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
