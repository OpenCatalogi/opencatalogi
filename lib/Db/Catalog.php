<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Catalog extends Entity implements JsonSerializable
{

	protected ?string $title 	   = null;
	protected ?string $reference   = null;
	protected ?string $summary     = null;
	protected ?string $description = null;
	protected ?string $image       = null;
	protected ?string $search	   = null;

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'reference', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'search', type: 'string');

	}

	public function hydrate(array $object): self
	{
		foreach($object as $key => $value) {
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
		return [
			'id' => $this->id,
			'title' => $this->title,
			'reference' => $this->reference,
			'summary' => $this->summary,
			'description' => $this->description,
			'image' => $this->image,
			'search' => $this->search,
		];
	}
}
