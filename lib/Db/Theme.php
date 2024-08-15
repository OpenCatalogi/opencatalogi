<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Theme extends Entity implements JsonSerializable
{

	protected ?string $title 	   = null;
	protected ?string $summary     = null;
	protected ?string $description = null;
	protected ?string $image       = null;

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'image', type: 'string');

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
//				("Error writing $key");
			}
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		$array = [
			'id' => $this->id,
			'title' => $this->title,
			'summary' => $this->summary,
			'description' => $this->description,
			'image' => $this->image,
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
