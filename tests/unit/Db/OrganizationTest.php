<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Organization;
use PHPUnit\Framework\TestCase;

class OrganizationTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $organization = new Organization();
        $organization->setTitle('Test Title');
        $organization->setSummary('Test Summary');
        $organization->setDescription('Test Description');
        $organization->setImage('Test Image');
        $organization->setOin('Test OIN');
        $organization->setTooi('Test TOOI');
        $organization->setRsin('Test RSIN');
        $organization->setPki('Test PKI');

        $this->assertEquals('Test Title', $organization->getTitle());
        $this->assertEquals('Test Summary', $organization->getSummary());
        $this->assertEquals('Test Description', $organization->getDescription());
        $this->assertEquals('Test Image', $organization->getImage());
        $this->assertEquals('Test OIN', $organization->getOin());
        $this->assertEquals('Test TOOI', $organization->getTooi());
        $this->assertEquals('Test RSIN', $organization->getRsin());
        $this->assertEquals('Test PKI', $organization->getPki());
    }

    public function testHydrate()
    {
        $data = [
            'title' => 'Hydrated Title',
            'summary' => 'Hydrated Summary',
            'description' => 'Hydrated Description',
            'image' => 'Hydrated Image',
            'oin' => 'Hydrated OIN',
            'tooi' => 'Hydrated TOOI',
            'rsin' => 'Hydrated RSIN',
            'pki' => 'Hydrated PKI',
        ];

        $organization = new Organization();
        $organization->hydrate($data);

        $this->assertEquals('Hydrated Title', $organization->getTitle());
        $this->assertEquals('Hydrated Summary', $organization->getSummary());
        $this->assertEquals('Hydrated Description', $organization->getDescription());
        $this->assertEquals('Hydrated Image', $organization->getImage());
        $this->assertEquals('Hydrated OIN', $organization->getOin());
        $this->assertEquals('Hydrated TOOI', $organization->getTooi());
        $this->assertEquals('Hydrated RSIN', $organization->getRsin());
        $this->assertEquals('Hydrated PKI', $organization->getPki());
    }

    public function testJsonSerialize()
    {
        $organization = new Organization();
        $organization->setTitle('JSON Title');
        $organization->setSummary('JSON Summary');
        $organization->setDescription('JSON Description');
        $organization->setImage('JSON Image');
        $organization->setOin('JSON OIN');
        $organization->setTooi('JSON TOOI');
        $organization->setRsin('JSON RSIN');
        $organization->setPki('JSON PKI');

        $expected = [
            'id' => null,
            'title' => 'JSON Title',
            'summary' => 'JSON Summary',
            'description' => 'JSON Description',
            'image' => 'JSON Image',
            'oin' => 'JSON OIN',
            'tooi' => 'JSON TOOI',
            'rsin' => 'JSON RSIN',
            'pki' => 'JSON PKI',
        ];

        $this->assertEquals($expected, $organization->jsonSerialize());
    }

    

}
