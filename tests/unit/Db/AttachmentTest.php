<?php

namespace OCA\OpenCatalogi\Tests\Db;

use DateTime;
use OCA\OpenCatalogi\Db\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    public function testConstruct()
    {
        $attachment = new Attachment();
        $this->assertInstanceOf(Attachment::class, $attachment);
    }

    public function testSettersAndGetters()
    {
        $attachment = new Attachment();

        $attachment->setId(1);
        $this->assertEquals(1, $attachment->getId());

        $attachment->setReference('reference');
        $this->assertEquals('reference', $attachment->getReference());

        $attachment->setTitle('title');
        $this->assertEquals('title', $attachment->getTitle());

        $attachment->setSummary('summary');
        $this->assertEquals('summary', $attachment->getSummary());

        $attachment->setDescription('description');
        $this->assertEquals('description', $attachment->getDescription());

        $attachment->setLabels(['label1', 'label2']);
        $this->assertEquals(['label1', 'label2'], $attachment->getLabels());

        $attachment->setAccessUrl('http://access.url');
        $this->assertEquals('http://access.url', $attachment->getAccessUrl());

        $attachment->setDownloadUrl('http://download.url');
        $this->assertEquals('http://download.url', $attachment->getDownloadUrl());

        $attachment->setType('type');
        $this->assertEquals('type', $attachment->getType());

        $attachment->setExtension('ext');
        $this->assertEquals('ext', $attachment->getExtension());

        $attachment->setSize(1234);
        $this->assertEquals(1234, $attachment->getSize());

        $attachment->setAnonymization(['anon1', 'anon2']);
        $this->assertEquals(['anon1', 'anon2'], $attachment->getAnonymization());

        $attachment->setLanguage(['en', 'fr']);
        $this->assertEquals(['en', 'fr'], $attachment->getLanguage());

        $attachment->setVersionOf('v1');
        $this->assertEquals('v1', $attachment->getVersionOf());

        $attachment->setHash('hashvalue');
        $this->assertEquals('hashvalue', $attachment->getHash());

        $publishedDate = new DateTime();
        $attachment->setPublished($publishedDate);
        $this->assertEquals($publishedDate, $attachment->getPublished());

        $modifiedDate = new DateTime();
        $attachment->setModified($modifiedDate);
        $this->assertEquals($modifiedDate, $attachment->getModified());

        $attachment->setLicense('MIT');
        $this->assertEquals('MIT', $attachment->getLicense());
    }

    public function testJsonSerialize()
    {
        $attachment = new Attachment();

        $attachment->setId(1);
        $attachment->setReference('reference');
        $attachment->setTitle('title');
        $attachment->setSummary('summary');
        $attachment->setDescription('description');
        $attachment->setLabels(['label1', 'label2']);
        $attachment->setAccessUrl('http://access.url');
        $attachment->setDownloadUrl('http://download.url');
        $attachment->setType('type');
        $attachment->setExtension('ext');
        $attachment->setSize(1234);
        $attachment->setAnonymization(['anon1', 'anon2']);
        $attachment->setLanguage(['en', 'fr']);
        $attachment->setVersionOf('v1');
        $attachment->setHash('hashvalue');
        $attachment->setPublished(new DateTime('2023-01-01T00:00:00+00:00'));
        $attachment->setModified(new DateTime('2023-01-02T00:00:00+00:00'));
        $attachment->setLicense('MIT');

        $expected = [
            'id' => 1,
            'reference' => 'reference',
            'title' => 'title',
            'summary' => 'summary',
            'description' => 'description',
            'labels' => ['label1', 'label2'],
            'accessUrl' => 'http://access.url',
            'downloadUrl' => 'http://download.url',
            'type' => 'type',
            'extension' => 'ext',
            'size' => 1234,
            'anonymization' => ['anon1', 'anon2'],
            'language' => ['en', 'fr'],
            'versionOf' => 'v1',
            'hash' => 'hashvalue',
            'published' => '2023-01-01T00:00:00+00:00',
            'modified' => '2023-01-02T00:00:00+00:00',
            'license' => 'MIT',
        ];

        $this->assertEquals($expected, $attachment->jsonSerialize());
    }

    public function testHydrate()
    {
        $data = [
            'id' => 1,
            'reference' => 'reference',
            'title' => 'title',
            'summary' => 'summary',
            'description' => 'description',
            'labels' => ['label1', 'label2'],
            'accessUrl' => 'http://access.url',
            'downloadUrl' => 'http://download.url',
            'type' => 'type',
            'extension' => 'ext',
            'size' => 1234,
            'anonymization' => ['anon1', 'anon2'],
            'language' => ['en', 'fr'],
            'versionOf' => 'v1',
            'hash' => 'hashvalue',
            'published' => new DateTime('2023-01-01T00:00:00+00:00'),
            'modified' => new DateTime('2023-01-02T00:00:00+00:00'),
            'license' => 'MIT',
        ];

        $attachment = new Attachment();
        $attachment->hydrate($data);

        $this->assertEquals(1, $attachment->getId());
        $this->assertEquals('reference', $attachment->getReference());
        $this->assertEquals('title', $attachment->getTitle());
        $this->assertEquals('summary', $attachment->getSummary());
        $this->assertEquals('description', $attachment->getDescription());
        $this->assertEquals(['label1', 'label2'], $attachment->getLabels());
        $this->assertEquals('http://access.url', $attachment->getAccessUrl());
        $this->assertEquals('http://download.url', $attachment->getDownloadUrl());
        $this->assertEquals('type', $attachment->getType());
        $this->assertEquals('ext', $attachment->getExtension());
        $this->assertEquals(1234, $attachment->getSize());
        $this->assertEquals(['anon1', 'anon2'], $attachment->getAnonymization());
        $this->assertEquals(['en', 'fr'], $attachment->getLanguage());
        $this->assertEquals('v1', $attachment->getVersionOf());
        $this->assertEquals('hashvalue', $attachment->getHash());
        $this->assertEquals(new DateTime('2023-01-01T00:00:00+00:00'), $attachment->getPublished());
        $this->assertEquals(new DateTime('2023-01-02T00:00:00+00:00'), $attachment->getModified());
        $this->assertEquals('MIT', $attachment->getLicense());
    }
}
