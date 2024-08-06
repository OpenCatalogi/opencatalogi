<?php

namespace OCA\OpenCatalogi\Tests\Db;

use OCA\OpenCatalogi\Db\Publication;
use PHPUnit\Framework\TestCase;
use DateTime;

class PublicationTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $publication = new Publication();
        $publication->setTitle('Test Title');
        $publication->setReference('Test Reference');
        $publication->setSummary('Test Summary');
        $publication->setDescription('Test Description');
        $publication->setImage('Test Image');
        $publication->setCategory('Test Category');
        $publication->setPortal('Test Portal');
        $publication->setCatalogi('Test Catalogi');
        $publication->setMetaData('Test MetaData');
        $publication->setPublished(new DateTime('2022-01-01T00:00:00Z'));
        $publication->setModified(new DateTime('2022-01-02T00:00:00Z'));
        $publication->setFeatured(true);
        $publication->setOrganization(['org1', 'org2']);
        $publication->setData(['data1', 'data2']);
        $publication->setAttachments(['attach1', 'attach2']);
        $publication->setAttachmentCount(2);
        $publication->setSchema('Test Schema');
        $publication->setStatus('Test Status');
        $publication->setLicense('Test License');
        $publication->setThemes(['theme1', 'theme2']);
        $publication->setAnonymization(['anon1', 'anon2']);
        $publication->setLanguageObject(['lang1', 'lang2']);

        $this->assertEquals('Test Title', $publication->getTitle());
        $this->assertEquals('Test Reference', $publication->getReference());
        $this->assertEquals('Test Summary', $publication->getSummary());
        $this->assertEquals('Test Description', $publication->getDescription());
        $this->assertEquals('Test Image', $publication->getImage());
        $this->assertEquals('Test Category', $publication->getCategory());
        $this->assertEquals('Test Portal', $publication->getPortal());
        $this->assertEquals('Test Catalogi', $publication->getCatalogi());
        $this->assertEquals('Test MetaData', $publication->getMetaData());
        $this->assertEquals('2022-01-01T00:00:00+00:00', $publication->getPublished()->format('c'));
        $this->assertEquals('2022-01-02T00:00:00+00:00', $publication->getModified()->format('c'));
        $this->assertTrue($publication->getFeatured());
        $this->assertEquals(['org1', 'org2'], $publication->getOrganization());
        $this->assertEquals(['data1', 'data2'], $publication->getData());
        $this->assertEquals(['attach1', 'attach2'], $publication->getAttachments());
        $this->assertEquals(2, $publication->getAttachmentCount());
        $this->assertEquals('Test Schema', $publication->getSchema());
        $this->assertEquals('Test Status', $publication->getStatus());
        $this->assertEquals('Test License', $publication->getLicense());
        $this->assertEquals(['theme1', 'theme2'], $publication->getThemes());
        $this->assertEquals(['anon1', 'anon2'], $publication->getAnonymization());
        $this->assertEquals(['lang1', 'lang2'], $publication->getLanguageObject());
    }

    public function testHydrate()
    {
        $data = [
            'title' => 'Hydrated Title',
            'reference' => 'Hydrated Reference',
            'summary' => 'Hydrated Summary',
            'description' => 'Hydrated Description',
            'image' => 'Hydrated Image',
            'category' => 'Hydrated Category',
            'portal' => 'Hydrated Portal',
            'catalogi' => 'Hydrated Catalogi',
            'metaData' => 'Hydrated MetaData',
            'published' => new DateTime('2022-01-01T00:00:00Z'),
            'modified' => new DateTime('2022-01-02T00:00:00Z'),
            'featured' => true,
            'organization' => ['org1', 'org2'],
            'data' => ['data1', 'data2'],
            'attachments' => ['attach1', 'attach2'],
            'attachmentCount' => 2,
            'schema' => 'Hydrated Schema',
            'status' => 'Hydrated Status',
            'license' => 'Hydrated License',
            'themes' => ['theme1', 'theme2'],
            'anonymization' => ['anon1', 'anon2'],
            'languageObject' => ['lang1', 'lang2'],
        ];

        $publication = new Publication();
        $publication->hydrate($data);

        $this->assertEquals('Hydrated Title', $publication->getTitle());
        $this->assertEquals('Hydrated Reference', $publication->getReference());
        $this->assertEquals('Hydrated Summary', $publication->getSummary());
        $this->assertEquals('Hydrated Description', $publication->getDescription());
        $this->assertEquals('Hydrated Image', $publication->getImage());
        $this->assertEquals('Hydrated Category', $publication->getCategory());
        $this->assertEquals('Hydrated Portal', $publication->getPortal());
        $this->assertEquals('Hydrated Catalogi', $publication->getCatalogi());
        $this->assertEquals('Hydrated MetaData', $publication->getMetaData());
        $this->assertEquals('2022-01-01T00:00:00+00:00', $publication->getPublished()->format('c'));
        $this->assertEquals('2022-01-02T00:00:00+00:00', $publication->getModified()->format('c'));
        $this->assertTrue($publication->getFeatured());
        $this->assertEquals(['org1', 'org2'], $publication->getOrganization());
        $this->assertEquals(['data1', 'data2'], $publication->getData());
        $this->assertEquals(['attach1', 'attach2'], $publication->getAttachments());
        $this->assertEquals(2, $publication->getAttachmentCount());
        $this->assertEquals('Hydrated Schema', $publication->getSchema());
        $this->assertEquals('Hydrated Status', $publication->getStatus());
        $this->assertEquals('Hydrated License', $publication->getLicense());
        $this->assertEquals(['theme1', 'theme2'], $publication->getThemes());
        $this->assertEquals(['anon1', 'anon2'], $publication->getAnonymization());
        $this->assertEquals(['lang1', 'lang2'], $publication->getLanguageObject());
    }

    public function testJsonSerialize()
    {
        $publication = new Publication();
        $publication->setTitle('JSON Title');
        $publication->setReference('JSON Reference');
        $publication->setSummary('JSON Summary');
        $publication->setDescription('JSON Description');
        $publication->setImage('JSON Image');
        $publication->setCategory('JSON Category');
        $publication->setPortal('JSON Portal');
        $publication->setCatalogi('JSON Catalogi');
        $publication->setMetaData('JSON MetaData');
        $publication->setPublished(new DateTime('2022-01-01T00:00:00Z'));
        $publication->setModified(new DateTime('2022-01-02T00:00:00Z'));
        $publication->setFeatured(true);
        $publication->setOrganization(['org1', 'org2']);
        $publication->setData(['data1', 'data2']);
        $publication->setAttachments(['attach1', 'attach2']);
        $publication->setAttachmentCount(2);
        $publication->setSchema('JSON Schema');
        $publication->setStatus('JSON Status');
        $publication->setLicense('JSON License');
        $publication->setThemes(['theme1', 'theme2']);
        $publication->setAnonymization(['anon1', 'anon2']);
        $publication->setLanguageObject(['lang1', 'lang2']);

        $expected = [
            'id' => null,
            'title' => 'JSON Title',
            'reference' => 'JSON Reference',
            'summary' => 'JSON Summary',
            'description' => 'JSON Description',
            'image' => 'JSON Image',
            'category' => 'JSON Category',
            'portal' => 'JSON Portal',
            'catalogi' => 'JSON Catalogi',
            'metaData' => 'JSON MetaData',
            'published' => '2022-01-01T00:00:00+00:00',
            'modified' => '2022-01-02T00:00:00+00:00',
            'featured' => true,
            'organization' => ['org1', 'org2'],
            'data' => ['data1', 'data2'],
            'attachments' => ['attach1', 'attach2'],
            'attachmentCount' => 2,
            'schema' => 'JSON Schema',
            'status' => 'JSON Status',
            'license' => 'JSON License',
            'themes' => ['theme1', 'theme2'],
            'anonymization' => ['anon1', 'anon2'],
            'languageObject' => ['lang1', 'lang2'],
        ];

        $this->assertEquals($expected, $publication->jsonSerialize());
    }

    public function testJsonSerializeWithNullValues()
    {
        $publication = new Publication();
        $publication->setTitle('JSON Title');
        $publication->setReference('JSON Reference');
        $publication->setSummary('JSON Summary');
        $publication->setDescription('JSON Description');
        $publication->setImage(null);
        $publication->setCategory(null);
        $publication->setPortal(null);
        $publication->setCatalogi(null);
        $publication->setMetaData(null);
        $publication->setPublished(new DateTime('2022-01-01T00:00:00Z'));
        $publication->setModified(new DateTime('2022-01-02T00:00:00Z'));
        $publication->setFeatured(null);
        $publication->setOrganization([]);
        $publication->setData([]);
        $publication->setAttachments([]);
        $publication->setAttachmentCount(1);
        $publication->setSchema(null);
        $publication->setStatus(null);
        $publication->setLicense(null);
        $publication->setThemes([]);
        $publication->setAnonymization([]);
        $publication->setLanguageObject([]);

        $expected = [
            'id' => null,
            'title' => 'JSON Title',
            'reference' => 'JSON Reference',
            'summary' => 'JSON Summary',
            'description' => 'JSON Description',
            'image' => null,
            'category' => null,
            'portal' => null,
            'catalogi' => null,
            'metaData' => null,
            'published' => '2022-01-01T00:00:00+00:00',
            'modified' => '2022-01-02T00:00:00+00:00',
            'featured' => null,
            'organization' => [],
            'data' => [],
            'attachments' => [],
            'attachmentCount' => 1,
            'schema' => null,
            'status' => null,
            'license' => null,
            'themes' => [],
            'anonymization' => [],
            'languageObject' => [],
        ];

        $this->assertEquals($expected, $publication->jsonSerialize());
    }
}
