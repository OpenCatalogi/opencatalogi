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
    private $urlGenerator;
    private $config;
    private $objectService;
    private $client;
    private $directoryService;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(IURLGenerator::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);

        $this->directoryService = new DirectoryService(
            $this->urlGenerator,
            $this->config,
            $this->objectService
        );

        // Use reflection to set the private $client property
        $this->client = $this->createMock(Client::class);
        $reflection = new \ReflectionClass($this->directoryService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->directoryService, $this->client);
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

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', 'key'],
                ['opencatalogi', 'mongodbCluster', 'cluster']
            ]);

        $this->objectService->method('findObjects')
            ->with(['_schema' => 'catalog'], $dbConfig)
            ->willReturn(['documents' => $catalogi]);

        $this->client->method('post')
            ->willReturn(new Response(200));

        $statusCode = $this->directoryService->registerToExternalDirectory($newDirectory);

        $this->assertEquals(200, $statusCode);
    }

    public function testFetchFromExternalDirectory()
    {
        $directory = ['directory' => 'https://example.com/directory'];
        $responseBody = json_encode(['results' => [['directory' => 'https://example.com/dir1'], ['directory' => 'https://example.com/dir2']]]);
        $response = new Response(200, [], $responseBody);

        $this->client->method('get')
            ->with($directory['directory'])
            ->willReturn($response);

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
