<?php

use OCA\OpenCatalogi\Db\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    private $attachment;

    protected function setUp(): void
    {
        $this->attachment = new Attachment();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Attachment::class, $this->attachment);
    }

    public function testHydrate()
    {
        $data = [
            'reference' => 'ref123',
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'labels' => ['label1', 'label2'],
            'accessUrl' => 'http://example.com/access',
            'downloadUrl' => 'http://example.com/download',
            'type' => 'document',
            'extension' => 'pdf',
            'size' => 1024,
            'versionOf' => 'v1.0',
            'hash' => 'abc123',
            'anonymization' => ['field1' => 'value1'],
            'language' => ['en', 'fr'],
            'modified' => new DateTime('2023-01-01T00:00:00Z'),
            'published' => new DateTime('2023-01-02T00:00:00Z'),
            'license' => 'MIT'
        ];

        $this->attachment->hydrate($data);

        $this->assertEquals('ref123', $this->attachment->getReference());
        $this->assertEquals('Test Title', $this->attachment->getTitle());
        $this->assertEquals('Test Summary', $this->attachment->getSummary());
        $this->assertEquals('Test Description', $this->attachment->getDescription());
        $this->assertEquals(['label1', 'label2'], $this->attachment->getLabels());
        $this->assertEquals('http://example.com/access', $this->attachment->getAccessUrl());
        $this->assertEquals('http://example.com/download', $this->attachment->getDownloadUrl());
        $this->assertEquals('document', $this->attachment->getType());
        $this->assertEquals('pdf', $this->attachment->getExtension());
        $this->assertEquals(1024, $this->attachment->getSize());
        $this->assertEquals('v1.0', $this->attachment->getVersionOf());
        $this->assertEquals('abc123', $this->attachment->getHash());
        $this->assertEquals(['field1' => 'value1'], $this->attachment->getAnonymization());
        $this->assertEquals(['en', 'fr'], $this->attachment->getLanguage());
        $this->assertEquals(new DateTime('2023-01-01T00:00:00Z'), $this->attachment->getModified());
        $this->assertEquals(new DateTime('2023-01-02T00:00:00Z'), $this->attachment->getPublished());
        $this->assertEquals('MIT', $this->attachment->getLicense());
    }

    public function testJsonSerialize()
    {
        $data = [
            'reference' => 'ref123',
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'labels' => ['label1', 'label2'],
            'accessUrl' => 'http://example.com/access',
            'downloadUrl' => 'http://example.com/download',
            'type' => 'document',
            'extension' => 'pdf',
            'size' => 1024,
            'versionOf' => 'v1.0',
            'hash' => 'abc123',
            'anonymization' => ['field1' => 'value1'],
            'language' => ['en', 'fr'],
            'modified' => new DateTime('2023-01-01T00:00:00Z'),
            'published' => new DateTime('2023-01-02T00:00:00Z'),
            'license' => 'MIT'
        ];

        $this->attachment->hydrate($data);

        $json = $this->attachment->jsonSerialize();

        $expected = [
            'id' => null,
            'reference' => 'ref123',
            'title' => 'Test Title',
            'summary' => 'Test Summary',
            'description' => 'Test Description',
            'labels' => ['label1', 'label2'],
            'accessUrl' => 'http://example.com/access',
            'downloadUrl' => 'http://example.com/download',
            'type' => 'document',
            'extension' => 'pdf',
            'size' => 1024,
            'versionOf' => 'v1.0',
            'hash' => 'abc123',
            'anonymization' => ['field1' => 'value1'],
            'language' => ['en', 'fr'],
            'modified' => '2023-01-01T00:00:00+00:00',
            'published' => '2023-01-02T00:00:00+00:00',
            'license' => 'MIT'
        ];

        $this->assertEquals($expected, $json);
    }
}

?>
