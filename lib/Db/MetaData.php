<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class MetaData extends Entity implements JsonSerializable
{

	protected ?string $title 	   = null;
	protected ?string $reference   = null;
	protected ?string $schema      = null;
	protected ?string $version     = null;
	protected ?string $type        = null;
	protected ?string $description = null;
	protected array   $required    = [];
	protected array   $properties  = [];

	public function __construct() {
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'reference', type: 'string');
		$this->addType(fieldName: 'schema', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'type', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'required', type: 'json');
		$this->addType(fieldName: 'properties', type: 'json');

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
			'id'          => $this->id,
			'title'       => $this->title,
			'reference'   => $this->reference,
			'schema'      => $this->schema,
			'version'     => $this->version,
			'type'        => $this->type,
			'description' => $this->description,
			'required'    => $this->required,
			'properties'  => $this->properties,
		];
	}
}
