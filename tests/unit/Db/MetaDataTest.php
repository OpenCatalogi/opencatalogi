<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\MetaData;
use PHPUnit\Framework\TestCase;

class MetaDataTest extends TestCase
{
    public function testConstruct()
    {
        $metaData = new MetaData();
        $this->assertInstanceOf(MetaData::class, $metaData);
    }

    public function testSettersAndGetters()
    {
        $metaData = new MetaData();

        $metaData->setTitle('Test Title');
        $this->assertEquals('Test Title', $metaData->getTitle());

        $metaData->setVersion('1.0.0');
        $this->assertEquals('1.0.0', $metaData->getVersion());

        $metaData->setDescription('Test Description');
        $this->assertEquals('Test Description', $metaData->getDescription());

        $required = ['field1', 'field2'];
        $metaData->setRequired($required);
        $this->assertEquals($required, $metaData->getRequired());

        $properties = ['property1' => 'value1', 'property2' => 'value2'];
        $metaData->setProperties($properties);
        $this->assertEquals($properties, $metaData->getProperties());
    }

    public function testJsonSerialize()
    {
        $metaData = new MetaData();

        $metaData->setId(1);
        $metaData->setTitle('Test Title');
        $metaData->setVersion('1.0.0');
        $metaData->setDescription('Test Description');
        $required = ['field1', 'field2'];
        $metaData->setRequired($required);
        $properties = ['property1' => 'value1', 'property2' => 'value2'];
        $metaData->setProperties($properties);

        $expected = [
            'id'          => 1,
            'title'       => 'Test Title',
            'version'     => '1.0.0',
            'description' => 'Test Description',
            'required'    => $required,
            'properties'  => $properties,
        ];

        $this->assertEquals($expected, $metaData->jsonSerialize());
    }

    public function testHydrate()
    {
        $data = [
            'id'          => 1,
            'title'       => 'Test Title',
            'version'     => '1.0.0',
            'description' => 'Test Description',
            'required'    => ['field1', 'field2'],
            'properties'  => ['property1' => 'value1', 'property2' => 'value2'],
        ];

        $metaData = new MetaData();
        $metaData->hydrate($data);

        $this->assertEquals(1, $metaData->getId());
        $this->assertEquals('Test Title', $metaData->getTitle());
        $this->assertEquals('1.0.0', $metaData->getVersion());
        $this->assertEquals('Test Description', $metaData->getDescription());
        $this->assertEquals(['field1', 'field2'], $metaData->getRequired());
        $this->assertEquals(['property1' => 'value1', 'property2' => 'value2'], $metaData->getProperties());
    }
}
