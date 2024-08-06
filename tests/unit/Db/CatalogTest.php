<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Catalog;
use PHPUnit\Framework\TestCase;

class CatalogTest extends TestCase
{
    public function testConstruct()
    {
        $catalog = new Catalog();
        $this->assertInstanceOf(Catalog::class, $catalog);
    }

    public function testSettersAndGetters()
    {
        $catalog = new Catalog();

        $catalog->setId(1);
        $this->assertEquals(1, $catalog->getId());

        $catalog->setTitle('Test Title');
        $this->assertEquals('Test Title', $catalog->getTitle());

        $catalog->setSummary('Test Summary');
        $this->assertEquals('Test Summary', $catalog->getSummary());

        $catalog->setDescription('Test Description');
        $this->assertEquals('Test Description', $catalog->getDescription());

        $catalog->setImage('http://image.url');
        $this->assertEquals('http://image.url', $catalog->getImage());

        $catalog->setSearch('search query');
        $this->assertEquals('search query', $catalog->getSearch());
    }

    public function testJsonSerialize()
    {
        $catalog = new Catalog();

        $catalog->setId(1);
        $catalog->setTitle('Test Title');
        $catalog->setSummary('Test Summary');
        $catalog->setDescription('Test Description');
        $catalog->setImage('http://image.url');
        $catalog->setSearch('search query');

        $expected = [
            'id' => 1,
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'image' => 'http://image.url',
            'search' => 'search query',
        ];

        $this->assertEquals($expected, $catalog->jsonSerialize());
    }

    public function testHydrate()
    {
        $data = [
            'id' => 1,
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'image' => 'http://image.url',
            'search' => 'search query',
        ];

        $catalog = new Catalog();
        $catalog->hydrate($data);

        $this->assertEquals(1, $catalog->getId());
        $this->assertEquals('Test Title', $catalog->getTitle());
        $this->assertEquals('Test Summary', $catalog->getSummary());
        $this->assertEquals('Test Description', $catalog->getDescription());
        $this->assertEquals('http://image.url', $catalog->getImage());
        $this->assertEquals('search query', $catalog->getSearch());
    }
}
