<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Publication extends Entity implements JsonSerializable
{

	protected ?string $title 	   		 = null;
	protected ?string $reference   		 = null;
	protected ?string $summary     		 = null;
	protected ?string $description 		 = null;
	protected ?string $image       		 = null;
	protected ?string $category    		 = null;
	protected ?string $portal      		 = null;
	protected ?string $catalogi    		 = null;
	protected ?string $metaData    		 = null;
	protected ?DateTime $publicationDate = null;
	protected ?DateTime $published = null;
	protected ?DateTime $modified        = null;
	protected ?string $featured          = null;
	protected ?array $organization       = [];
	protected ?array $data               = [];
	protected ?array $attachments        = [];
	protected int $attachmentCount       = 0;
	protected ?string $schema            = null;
	protected ?string $status            = null;
	protected ?string $license           = null;
	protected ?array $themes             = [];
	protected ?array $anonymization      = [];
	protected ?array $languageObject     = [];

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
		$this->addType(fieldName: 'published', type: 'datetime');
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

//			var_dump($method);
//			if(method_exists(object_or_class: $this, method: $method) === false) {
//				continue;
//			}
			$this->$method($value);
		}

		$this->setAttachmentCount(0);
		if($this->attachments !== null) {
			$this->setAttachmentCount(count($this->getAttachments()));
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
			'category' => $this->category,
			'portal' => $this->portal,
			'catalogi' => $this->catalogi,
			'metaData' => $this->metaData,
			'publicationDate' => $this->publicationDate->format('c'),
			'modified'	=> $this->modified->format('c'),
			'featured' => $this->featured,
			'organization' => $this->organization,
			'data' => $this->data,
			'attachments' => $this->attachments,
			'attachmentCount' => $this->attachmentCount,
			'schema' => $this->schema,
			'status' => $this->status,
			'license' => $this->license,
			'themes' => $this->themes,
			'anonymization' => $this->anonymization,
			'languageObject' => $this->languageObject,
		];
	}
}
