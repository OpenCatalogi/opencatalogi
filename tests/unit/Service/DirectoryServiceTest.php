<?php

namespace OCA\OpenCatalogi\Tests\Service;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DirectoryServiceTest extends TestCase
{
    /** @var MockObject|IURLGenerator */
    private $urlGenerator;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var MockObject|Client */
    private $client;

    /** @var DirectoryService */
    private $directoryService;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(IURLGenerator::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->client = $this->createMock(Client::class);

        $this->directoryService = new DirectoryService(
            $this->urlGenerator,
            $this->config,
            $this->objectService
        );

        // Replace the client with the mock
        $reflection = new \ReflectionClass($this->directoryService);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->directoryService, $this->client);
    }

    public function testGetDirectoryEntry()
    {
        $catalogId = 'testCatalogId';

        $this->urlGenerator
            ->method('getAbsoluteURL')
            ->willReturn('http://example.com');

        $entry = $this->invokeMethod($this->directoryService, 'getDirectoryEntry', [$catalogId]);

        $this->assertIsArray($entry);
        $this->assertEquals('http://example.com', $entry['search']);
        $this->assertEquals('http://example.com', $entry['directory']);
        $this->assertEquals($catalogId, $entry['catalogId']);
    }

    public function testRegisterToExternalDirectory()
    {
        $newDirectory = ['directory' => 'http://example.com'];

        $this->config
            ->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://mongo'],
                ['opencatalogi', 'mongodbKey', '', 'key'],
                ['opencatalogi', 'mongodbCluster', '', 'cluster']
            ]);

        $catalogi = [['id' => '1']];
        $this->objectService
            ->method('findObjects')
            ->willReturn(['documents' => $catalogi]);

        $this->client
            ->method('post')
            ->willReturn(new Response(200));

        $statusCode = $this->directoryService->registerToExternalDirectory($newDirectory);

        $this->assertEquals(200, $statusCode);
    }

    public function testCreateDirectoryFromResult()
    {
        $result = ['directory' => 'http://external-directory.com'];

        $this->config
            ->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://mongo'],
                ['opencatalogi', 'mongodbKey', '', 'key'],
                ['opencatalogi', 'mongodbCluster', '', 'cluster']
            ]);

        $this->objectService
            ->method('saveObject')
            ->willReturn(['id' => '1']);

        $this->urlGenerator
            ->method('getAbsoluteURL')
            ->willReturn('http://example.com');

        $directory = $this->invokeMethod($this->directoryService, 'createDirectoryFromResult', [$result]);

        $this->assertIsArray($directory);
        $this->assertEquals('1', $directory['id']);
    }

    public function testFetchFromExternalDirectory()
    {
        $directory = ['directory' => 'http://external-directory.com'];

        $response = new Response(200, [], json_encode(['results' => [['id' => '1']]]));
        $this->client
            ->method('get')
            ->willReturn($response);

        $results = $this->directoryService->fetchFromExternalDirectory($directory);

        $this->assertIsArray($results);
        $this->assertCount(1, $results);
        $this->assertEquals('1', $results[0]['id']);
    }

    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
