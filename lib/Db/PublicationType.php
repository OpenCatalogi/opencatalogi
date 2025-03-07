<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;
use OCP\IURLGenerator;

class PublicationType extends Entity implements JsonSerializable
{
	protected ?string $uuid        = null;
	protected ?string $uri         = null;
	protected ?string $version     = '0.0.1';
	protected ?string $title       = null;
	protected ?string $description = null;
	protected ?array  $required    = [];
	protected ?array  $properties  = [];
	protected ?string $source      = null;
	protected ?string $summary     = null;
	protected ?array  $archive     = [];
	protected ?DateTime $updated   = null;
	protected ?DateTime $created   = null;

	public function __construct() {
		$this->addType(fieldName: 'uuid', type: 'string');
		$this->addType(fieldName: 'uri', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'required', type: 'json');
		$this->addType(fieldName: 'properties', type: 'json');
		$this->addType(fieldName: 'source', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'archive', type: 'json');
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

		foreach ($object as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === []) {
				$value = null;
			}

			$method = 'set' . ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
				// Handle or log the exception as needed
			}
		}

		return $this;
	}

	/**
	 * Serializes the schema to an array
	 *
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		$required = $this->required ?? [];
        $properties = [];
		if (isset($this->properties) === true) {
			foreach ($this->properties as $key => $property) {
				$title = $property['title'] ?? $key;
				if ($property['required'] === true && in_array($title, $required) === false) {
					$required[] = $title;
				}

				$properties[$title] = $property;
			}
		}

		$array = [
			'id'          => $this->id,
			'uri'         => $this->uri,
			'uuid'        => $this->uuid,
			'version'     => $this->version,
			'title'       => $this->title,
			'description' => $this->description,
			'required'    => $required,
			'properties'  => $properties,
			'source'      => $this->source,
			'summary'     => $this->summary,
			'archive'     => $this->archive,
			'updated'     => $this->updated?->format('c'),
			'created'     => $this->created?->format('c'),
		];

		$jsonFields = $this->getJsonFields();

		foreach ($array as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === null) {
				$array[$key] = [];
			}
		}

		return $array;
	}

	/**
	 * Generate a JSON-Schema definition for the data field of a publication.
	 *
	 * @param IURLGenerator $urlGenerator An URL generator to generate the identifier of the schema.
	 *
	 * @return object The JSON-Schema object defining the data field of a publication.
	 */
	public function getSchemaObject(IURLGenerator $urlGenerator): object
	{
		$data = $this->jsonSerialize();
		unset($data['id'], $data['uuid'], $data['summary'], $data['archive'], $data['source'],
			$data['updated'], $data['created']);

		$data['type'] = 'object';

        // required on properties will break the validator, only have it set on object level
        // array_filter is used so that empty string or 0 validation rules are removed, so we dont validate what we didnt set
        if (isset($data['properties']) === true && empty($data['properties']) === false) {
            foreach ($data['properties'] as $key => $property) {
				$title = $property['title'] ?? $key;
                $data['properties'][$title] = array_filter($property);
                if (array_key_exists('required', $data['properties'][$key])) {
                    unset($data['properties'][$key]['required']);
                }
            }
        }

		// Validator needs this specific $schema
		$data['$schema'] = 'https://json-schema.org/draft/2020-12/schema';
		$data['$id'] = $urlGenerator->getAbsoluteURL($urlGenerator->linkToRoute('opencatalogi.publication_types.show', ['id' => $this->getUuid()]));
    
		return json_decode(json_encode($data));
	}
}
