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
	protected ?string $image       = null;
	protected ?string $search	   = null;

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'reference', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'image', type: 'string');
		$this->addType(fieldName: 'category', type: 'string');
		$this->addType(fieldName: 'portal', type: 'string');
		$this->addType(fieldName: 'catalogi', type: 'string');
		$this->addType(fieldName: 'metaData', type: 'string');
		$this->addType(fieldName: 'publicationDate', type: 'datetime');
		$this->addType(fieldName: 'modified', type: 'datetime');
		$this->addType(fieldName: 'featured', type: 'boolean');
		$this->addType(fieldName: 'organization', type: 'json');
		$this->addType(fieldName: 'data', type: 'json');
		$this->addType(fieldName: 'attachments', type: 'json');
		$this->addType(fieldName: 'attachmentCount', type: 'integer');
		$this->addType(fieldName: 'schema', type: 'string');
		$this->addType(fieldName: 'status', type: 'string');
		$this->addType(fieldName: 'license', type: 'string');
		$this->addType(fieldName: 'themes', type: 'json');
		$this->addType(fieldName: 'anonymization', type: 'json');
		$this->addType(fieldName: 'languageObject', type: 'json');

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
