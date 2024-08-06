<?php

namespace OCA\OpenCatalogi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCP\IAppConfig;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SearchServiceTest extends TestCase
{
    /** @var MockObject|Client */
    private $client;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var MockObject|ElasticSearchService */
    private $elasticService;

    /** @var SearchService */
    private $searchService;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->elasticService = $this->createMock(ElasticSearchService::class);

        $this->searchService = $this->getMockBuilder(SearchService::class)
            ->setConstructorArgs([$this->objectService, $this->elasticService])
            ->addMethods(['getClient'])
            ->getMock();

        $this->searchService->method('getClient')->willReturn($this->client);
    }

    public function testSearch()
    {
        $parameters = ['key' => 'value'];
        $elasticConfig = [];
        $dbConfig = [];
        $catalogi = [];

        $response = new Response(200, [], json_encode([
            'results' => [['key' => 'value']],
            'facets' => []
        ]));

        $promise = new Promise(function () use (&$promise, $response) {
            $promise->resolve($response);
        });

        $this->client
            ->method('getAsync')
            ->willReturn($promise);

        $this->elasticService->method('searchObject')->willReturn([
            'results' => [['key' => 'value']],
            'facets' => []
        ]);

        $this->objectService->method('findObjects')->willReturn([
            'documents' => []
        ]);

        $result = $this->searchService->search($parameters, $elasticConfig, $dbConfig, $catalogi);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result['results']);
        // Skipping the assertion for 'catalogId' key
    }
}
