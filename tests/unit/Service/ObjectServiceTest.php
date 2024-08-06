<?php

namespace OCA\OpenCatalogi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use OCA\OpenCatalogi\Service\ObjectService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ObjectServiceTest extends TestCase
{
    /** @var MockObject|Client */
    private $client;

    /** @var ObjectService */
    private $objectService;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);

        $this->objectService = $this->getMockBuilder(ObjectService::class)
            ->onlyMethods(['getClient'])
            ->getMock();

        // Replace the client with the mock
        $this->objectService->method('getClient')->willReturn($this->client);
    }

    public function testSaveObject()
    {
        $data = ['key' => 'value'];
        $config = [
            'mongodbCluster' => 'testCluster',
            'base_uri' => 'http://example.com',
            'headers' => ['api-key' => 'testKey']
        ];

        $response = new Response(200, [], json_encode(['insertedId' => 'testId']));
        $this->client
            ->method('post')
            ->willReturn($response);

        $result = $this->objectService->saveObject($data, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('document', $result);
    }

    public function testFindObjects()
    {
        $filters = ['key' => 'value'];
        $config = [
            'mongodbCluster' => 'testCluster',
            'base_uri' => 'http://example.com',
            'headers' => ['api-key' => 'testKey']
        ];

        $response = new Response(200, [], json_encode(['documents' => [['key' => 'value']]]));
        $this->client
            ->method('post')
            ->willReturn($response);

        $result = $this->objectService->findObjects($filters, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('documents', $result);
    }

    public function testFindObject()
    {
        $filters = ['_id' => 'testId'];
        $config = [
            'mongodbCluster' => 'testCluster',
            'base_uri' => 'http://example.com',
            'headers' => ['api-key' => 'testKey']
        ];

        $response = new Response(200, [], json_encode(['key' => 'value']));
        $this->client
            ->method('post')
            ->willReturn($response);

        $result = $this->objectService->findObject($filters, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('document', $result);
    }

    public function testUpdateObject()
    {
        $filters = ['_id' => 'testId'];
        $update = ['key' => 'newValue'];
        $config = [
            'mongodbCluster' => 'testCluster',
            'base_uri' => 'http://example.com',
            'headers' => ['api-key' => 'testKey']
        ];

        $response = new Response(200, [], json_encode(['modifiedCount' => 1]));
        $this->client
            ->method('post')
            ->willReturn($response);

        $result = $this->objectService->updateObject($filters, $update, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('document', $result);
    }

    public function testDeleteObject()
    {
        $filters = ['_id' => 'testId'];
        $config = [
            'mongodbCluster' => 'testCluster',
            'base_uri' => 'http://example.com',
            'headers' => ['api-key' => 'testKey']
        ];

        $response = new Response(200, [], json_encode(['deletedCount' => 1]));
        $this->client
            ->method('post')
            ->willReturn($response);

        $result = $this->objectService->deleteObject($filters, $config);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testAggregateObjects()
    {
        $filters = ['key' => 'value'];
        $pipeline = [];
        $config = [
            'mongodbCluster' => 'testCluster',
            'base_uri' => 'http://example.com',
            'headers' => ['api-key' => 'testKey']
        ];

        $response = new Response(200, [], json_encode(['documents' => [['key' => 'value']]]));
        $this->client
            ->method('post')
            ->willReturn($response);

        $result = $this->objectService->aggregateObjects($filters, $pipeline, $config);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('documents', $result);
    }
}
