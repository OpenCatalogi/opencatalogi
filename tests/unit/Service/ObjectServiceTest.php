<?php

namespace Service;

use OCA\OpenCatalogi\Service\ObjectService;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class ObjectServiceTest extends TestCase
{
    protected $clientMock;
    protected $objectService;

    protected function setUp(): void
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->objectService = $this->getMockBuilder(ObjectService::class)
            ->onlyMethods(['getClient'])
            ->getMock();
        $this->objectService->method('getClient')->willReturn($this->clientMock);
    }

    public function testSaveObject()
    {
        $config = ['mongodbCluster' => 'testCluster'];
        $data = ['key' => 'value'];

        $responseBody = json_encode(['insertedId' => '12345']);
        $response = new Response(200, [], $responseBody);
        $findObjectResponseBody = json_encode(['key' => 'value']);
        $findObjectResponse = new Response(200, [], $findObjectResponseBody);

        $this->clientMock->expects($this->exactly(2))
            ->method('post')
            ->willReturnOnConsecutiveCalls($response, $findObjectResponse);

        $result = $this->objectService->saveObject($data, $config);

        $this->assertEquals(['document' => ['key' => 'value']], $result);
    }

    public function testFindObjects()
    {
        $config = ['mongodbCluster' => 'testCluster'];
        $filters = ['key' => 'value'];

        $responseBody = json_encode(['documents' => [['key' => 'value']]]);
        $response = new Response(200, [], $responseBody);
        $this->clientMock->method('post')->willReturn($response);

        $result = $this->objectService->findObjects($filters, $config);

        $this->assertEquals(['documents' => [['key' => 'value']]], $result);
    }

    public function testFindObject()
    {
        $config = ['mongodbCluster' => 'testCluster'];
        $filters = ['key' => 'value'];

        $responseBody = json_encode(['key' => 'value']);
        $response = new Response(200, [], $responseBody);
        $this->clientMock->method('post')->willReturn($response);

        $result = $this->objectService->findObject($filters, $config);

        $this->assertEquals(['document' => ['key' => 'value']], $result);
    }

    public function testUpdateObject()
    {
        $config = ['mongodbCluster' => 'testCluster'];
        $filters = ['key' => 'value'];
        $update = ['key' => 'newValue'];

        $responseBody = json_encode(['matchedCount' => 1, 'modifiedCount' => 1]);
        $response = new Response(200, [], $responseBody);
        $findObjectResponseBody = json_encode(['key' => 'newValue']);
        $findObjectResponse = new Response(200, [], $findObjectResponseBody);

        $this->clientMock->expects($this->exactly(2))
            ->method('post')
            ->willReturnOnConsecutiveCalls($response, $findObjectResponse);

        $result = $this->objectService->updateObject($filters, $update, $config);

        $this->assertEquals(['document' => ['key' => 'newValue']], $result);
    }

    public function testDeleteObject()
    {
        $config = ['mongodbCluster' => 'testCluster'];
        $filters = ['key' => 'value'];

        $response = new Response(200, [], json_encode([]));
        $this->clientMock->method('post')->willReturn($response);

        $result = $this->objectService->deleteObject($filters, $config);

        $this->assertEquals([], $result);
    }

    public function testAggregateObjects()
    {
        $config = ['mongodbCluster' => 'testCluster'];
        $filters = ['key' => 'value'];
        $pipeline = [['$match' => ['key' => 'value']]];

        $responseBody = json_encode([['key' => 'value']]);
        $response = new Response(200, [], $responseBody);
        $this->clientMock->method('post')->willReturn($response);

        $result = $this->objectService->aggregateObjects($filters, $pipeline, $config);

        $this->assertEquals([['key' => 'value']], $result);
    }
}
