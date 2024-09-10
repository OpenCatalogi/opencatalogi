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
	protected ?DateTime $published       = null;
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

        $this->setStatus('concept');
		$this->setAttachments(null);
		$this->setOrganization(null);
		$this->setData(null);
		$this->setModified(new DateTime());


		if (isset($object['published']) === false) {
			$object['published'] = null;
		}

		// Todo: MetaData is depricated, we should use Schema instead. But this needs front-end changes as well.
		if (empty($object['schema']) === true) {
			$object['schema'] = $object['metaData'] ?? $this->getMetaData();
		}

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

		$this->setAttachmentCount('0');
		if($this->attachments !== null) {
			$this->setAttachmentCount(count($this->getAttachments()));
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
			'image' => $this->image,
			'category' => $this->category,
			'portal' => $this->portal,
			'catalogi' => json_decode($this->catalogi, true),
			'metaData' => $this->metaData,
			'published' => $this->published?->format('c'),
			'modified'	=> $this->modified?->format('c'),
			'featured' => $this->featured !== null ? (bool) $this->featured : null,
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

		$jsonFields = $this->getJsonFields();

		foreach ($array as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === null) {
				$array[$key] = [];
			}
		}

		return $array;
	}

    public function getStatus(): ?string {
        return $this->status;
    }
}
