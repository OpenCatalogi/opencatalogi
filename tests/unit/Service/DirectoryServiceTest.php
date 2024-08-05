<?php

use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use Test\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class DirectoryServiceTest extends TestCase
{
    private $urlGeneratorMock;
    private $configMock;
    private $objectServiceMock;
    private $clientMock;
    private $directoryService;

    protected function setUp(): void
    {
        $this->urlGeneratorMock = $this->createMock(IURLGenerator::class);
        $this->configMock = $this->createMock(IAppConfig::class);
        $this->objectServiceMock = $this->createMock(ObjectService::class);

        $this->configMock->method('getValueString')
            // ->willReturnMap([
            //     ['opencatalogi', 'mongodbLocation', 'http://localhost'],
            //     ['opencatalogi', 'mongodbKey', 'key'],
            //     ['opencatalogi', 'mongodbCluster', 'cluster']
            // ]);
            ->will($this->returnValueMap([
                ['opencatalogi', 'mongodbLocation', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', 'key'],
                ['opencatalogi', 'mongodbCluster', 'cluster']
            ]));

        // Debugging: check the parameters passed to the mocked method
        $location = $this->configMock->getValueString('opencatalogi', 'mongodbLocation');
        $key = $this->configMock->getValueString('opencatalogi', 'mongodbKey');
        $cluster = $this->configMock->getValueString('opencatalogi', 'mongodbCluster');

        print_r($location); // Should output 'http://localhost'
        print_r($key);      // Should output 'key'
        print_r($cluster);  // Should output 'cluster'

        $this->assertEquals('http://localhost', $location);
        $this->assertEquals('key', $key);
        $this->assertEquals('cluster', $cluster);

        $this->directoryService = new DirectoryService(
            $this->urlGeneratorMock,
            $this->configMock,
            $this->objectServiceMock
        );

        // Use reflection to set the private $client property
        $this->clientMock = $this->createMock(Client::class);

        $reflection = new ReflectionClass($this->directoryService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->directoryService, $this->clientMock);
    }

    public function testRegisterToExternalDirectory()
    {
        $newDirectory = ['directory' => 'https://example.com/directory'];
        $dbConfig = [
            'base_uri' => 'http://localhost',
            'headers' => ['api-key' => 'key'],
            'mongodbCluster' => 'cluster'
        ];

        $catalogi = [['id' => 'catalog1'], ['id' => 'catalog2']];

        $this->objectServiceMock->method('findObjects')
            ->with(['_schema' => 'catalog'], $dbConfig)
            ->willReturn(['documents' => $catalogi]);

        $this->clientMock->method('post')
            ->willReturn(new Response(200));

		$this->client->method('get')
			->willReturn(new Response(status: 200, body: '{"results": []}'));

        $statusCode = $this->directoryService->registerToExternalDirectory($newDirectory);

        $this->assertEquals(200, $statusCode);
    }

    public function testFetchFromExternalDirectory()
    {
        $directory = ['directory' => 'https://example.com/directory'];
        $responseBody = json_encode(['results' => [['directory' => 'https://example.com/dir1'], ['directory' => 'https://example.com/dir2']]]);
        $responseBody2 = json_encode(['results' => [['directory' => 'https://example.com/directory']]]);
        $response = new Response(status: 200, body: $responseBody);

        $this->clientMock->method('get')
            ->with($directory['directory'])
            ->willReturn($response);

		$this->objectService->method('findObjects')
			->willReturn(['documents' => []]);

        $results = $this->directoryService->fetchFromExternalDirectory($directory);

        $this->assertCount(2, $results);
    }

    public function testCreateDirectoryFromResult()
    {
        $result = [
            'directory' => 'https://example.com/directory',
            '_schema' => 'directory'
        ];
        $dbConfig = [
            'base_uri' => 'http://localhost',
            'headers' => ['api-key' => 'key'],
            'mongodbCluster' => 'cluster'
        ];

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', 'key'],
                ['opencatalogi', 'mongodbCluster', 'cluster']
            ]);

        $this->objectService->method('saveObject')
            ->with($result, $dbConfig)
            ->willReturn($result);

        $this->directoryService->method('registerToExternalDirectory')
            ->with($result)
            ->willReturn(200);

        $createdDirectory = $this->invokeMethod($this->directoryService, 'createDirectoryFromResult', [$result]);

        $this->assertNotNull($createdDirectory);
        $this->assertEquals($result, $createdDirectory);
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}