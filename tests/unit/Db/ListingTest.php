<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Listing;
use PHPUnit\Framework\TestCase;
use DateTime;

class ListingTest extends TestCase
{
    public function testConstruct()
    {
        $listing = new Listing();
        $this->assertInstanceOf(Listing::class, $listing);
    }

    public function testSettersAndGetters()
    {
        $listing = new Listing();

        $listing->setId(1);
        $this->assertEquals(1, $listing->getId());

        $listing->setTitle('Test Title');
        $this->assertEquals('Test Title', $listing->getTitle());

        $listing->setSummary('Test Summary');
        $this->assertEquals('Test Summary', $listing->getSummary());

        $listing->setDescription('Test Description');
        $this->assertEquals('Test Description', $listing->getDescription());

        $listing->setSearch('search query');
        $this->assertEquals('search query', $listing->getSearch());

        $listing->setDirectory('directory');
        $this->assertEquals('directory', $listing->getDirectory());

        $listing->setMetadata('metadata');
        $this->assertEquals('metadata', $listing->getMetadata());

        $listing->setStatus('status');
        $this->assertEquals('status', $listing->getStatus());

        $dateTime = new DateTime();
        $listing->setLastSync($dateTime);
        $this->assertEquals($dateTime, $listing->getLastSync());

        $listing->setDefault(true);
        $this->assertTrue($listing->getDefault());

        $listing->setAvailable(true);
        $this->assertTrue($listing->getAvailable());
    }

    public function testJsonSerialize()
    {
        $listing = new Listing();

        $listing->setId(1);
        $listing->setTitle('Test Title');
        $listing->setSummary('Test Summary');
        $listing->setDescription('Test Description');
        $listing->setSearch('search query');
        $listing->setDirectory('directory');
        $listing->setMetadata('metadata');
        $listing->setStatus('status');
        $dateTime = new DateTime();
        $listing->setLastSync($dateTime);
        $listing->setDefault(true);
        $listing->setAvailable(true);

        $expected = [
            'id'          => 1,
            'title'       => 'Test Title',
            'summary'     => 'Test Summary',
            'description' => 'Test Description',
            'search'      => 'search query',
            'directory'   => 'directory',
            'metadata'    => 'metadata',
            'status'      => 'status',
            'lastSync'    => $dateTime->format('c'),
            'default'     => true,
            'available'   => true,
        ];

        // $this->assertEquals($expected, $listing->jsonSerialize());
    }

    public function testHydrate()
    {
        $data = [
            'id'          => 1,
            'title'       => 'Test Title',
            'summary'     => 'Test Summary',
            'description' => 'Test Description',
            'search'      => 'search query',
            'directory'   => 'directory',
            'metadata'    => 'metadata',
            'status'      => 'status',
            'lastSync'    => (new DateTime())->format('c'),
            'default'     => true,
            'available'   => true,
        ];

        $listing = new Listing();
        $listing->hydrate($data);

        $this->assertEquals(1, $listing->getId());
        $this->assertEquals('Test Title', $listing->getTitle());
        $this->assertEquals('Test Summary', $listing->getSummary());
        $this->assertEquals('Test Description', $listing->getDescription());
        $this->assertEquals('search query', $listing->getSearch());
        $this->assertEquals('directory', $listing->getDirectory());
        $this->assertEquals('metadata', $listing->getMetadata());
        $this->assertEquals('status', $listing->getStatus());
        $this->assertEquals((new DateTime($data['lastSync']))->format('c'), $listing->getLastSync()->format('c'));
        $this->assertTrue($listing->getDefault());
        $this->assertTrue($listing->getAvailable());
    }
}
