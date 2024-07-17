<?php

use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use PHPUnit\Framework\TestCase;
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
		$this->client = $this->createMock(Client::class);

		$this->directoryService = new DirectoryService(
			$this->urlGenerator,
			$this->config,
			$this->objectService
		);

		// Use reflection to inject the mock client
		$reflection = new ReflectionClass($this->directoryService);
		$property = $reflection->getProperty('client');
		$property->setAccessible(true);
		$property->setValue($this->directoryService, $this->client);
	}

	public function testRegisterToExternalDirectory()
	{
		$newDirectory = ['directory' => 'http://example.com'];

		$this->config->method('getValueString')
			->will($this->returnValueMap([
				['opencatalogi', 'mongodbLocation', 'http://mongodb.example.com'],
				['opencatalogi', 'mongodbKey', 'some-api-key'],
				['opencatalogi', 'mongodbCluster', 'some-cluster']
			]));

		$this->objectService->method('findObjects')
			->willReturn(['documents' => [['id' => '123']]]);

		$this->urlGenerator->method('getAbsoluteURL')
			->will($this->returnValue('http://example.com'));

		$response = new Response(200, [], '');
		$this->client->method('post')
			->willReturn($response);

		$this->client->method('get')
			->willReturn($response);

		$statusCode = $this->directoryService->registerToExternalDirectory($newDirectory);

		$this->assertEquals(200, $statusCode);
	}

	public function testFetchFromExternalDirectory()
	{
		$directory = ['directory' => 'http://example.com'];

		$responseBody = json_encode(['results' => [['directory' => 'http://example.com/dir1']]]);
		$response = new Response(200, [], $responseBody);

		$this->client->method('get')
			->willReturn($response);

		$this->objectService->method('saveObject')
			->willReturn(['_id' => 'some_id']);

		$this->urlGenerator->method('getAbsoluteURL')
			->will($this->returnValue('http://example.com'));

		$results = $this->directoryService->fetchFromExternalDirectory($directory);

		$this->assertCount(1, $results);
		$this->assertEquals('http://example.com/dir1', $results[0]->directory);
	}
}
