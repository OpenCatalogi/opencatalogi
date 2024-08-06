<?php

namespace OCA\OpenCatalogi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SearchServiceTest extends TestCase
{
    /** @var MockObject|Client */
    private $client;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var MockObject|ElasticSearchService */
    private $elasticSearchService;

    /** @var SearchService */
    private $searchService;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->searchService = new SearchService($this->objectService, $this->elasticSearchService);
        // Replace the client with the mock
        $this->searchService->client = $this->client;
    }

    public function testSearch()
    {
        $parameters = ['key' => 'value'];
        $elasticConfig = ['some' => 'config'];
        $dbConfig = ['some' => 'config'];

        // Mocking localResults for the sake of the test, assuming this is what ElasticSearch would return
        $localResults = [
            'results' => [
                ['key' => 'value1', '_score' => 1],
                ['key' => 'value2', '_score' => 2]
            ],
            'facets' => []
        ];

        // Mocking the ElasticSearchService's searchObject method to return the mocked localResults
        $this->elasticSearchService->expects($this->once())
            ->method('searchObject')
            ->with($parameters, $elasticConfig)
            ->willReturn($localResults);

        $directory = [
            'documents' => [
                ['search' => 'http://example.com/search', 'catalogId' => 'catalog1', 'default' => true]
            ]
        ];

        $this->objectService->expects($this->once())
            ->method('findObjects')
            ->with(['_schema' => 'directory'], $dbConfig)
            ->willReturn($directory);

        $response = new Response(200, [], json_encode(['results' => [], 'facets' => []]));
        $this->client->expects($this->once())
            ->method('getAsync')
            ->willReturn(new FulfilledPromise($response));

        $result = $this->searchService->search($parameters, $elasticConfig, $dbConfig);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('facets', $result);
        $this->assertCount(2, $result['results']);
    }
}
