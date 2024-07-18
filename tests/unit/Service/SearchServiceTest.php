<?php

use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use Test\TestCase; 
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Response;
use OCA\OpenCatalogi\Service\ElasticSearchService;

class SearchServiceTest extends TestCase
{
    private $objectServiceMock;
    private $elasticSearchServiceMock;
    private $searchService;

    protected function setUp(): void
    {
        $this->objectServiceMock = $this->createMock(ObjectService::class);
        $this->elasticSearchServiceMock = $this->createMock(ElasticSearchService::class);

        $this->searchService = new SearchService($this->objectServiceMock, $this->elasticSearchServiceMock);
    }

    public function testSortResultArray()
    {
        $a = ['_score' => 1];
        $b = ['_score' => 2];
        $this->assertEquals(-1, $this->searchService->sortResultArray($a, $b));
        $this->assertEquals(1, $this->searchService->sortResultArray($b, $a));
        $this->assertEquals(0, $this->searchService->sortResultArray($a, $a));
    }

    public function testMergeFacets()
    {
        $existingAggregation = [
            ['_id' => 'category1', 'count' => 10],
            ['_id' => 'category2', 'count' => 5]
        ];
        $newAggregation = [
            ['_id' => 'category1', 'count' => 3],
            ['_id' => 'category3', 'count' => 7]
        ];

        $expected = [
            ['_id' => 'category1', 'count' => 13],
            ['_id' => 'category2', 'count' => 5],
            ['_id' => 'category3', 'count' => 7]
        ];

        $result = $this->searchService->mergeFacets($existingAggregation, $newAggregation);

        $this->assertEquals($expected, $result);
    }

    public function testSearch()
    {
        $localResults = [
            'results' => [
                ['_score' => 1, 'id' => 1],
                ['_score' => 2, 'id' => 2]
            ],
            'facets' => [
                ['_id' => 'category1', 'count' => 10],
                ['_id' => 'category2', 'count' => 5]
            ]
        ];

        $this->objectServiceMock
            ->method('findObjects')
            ->willReturn(['documents' => [['default' => true, 'search' => 'http://example.com/search']]]);

        $this->elasticSearchServiceMock
            ->method('searchObject')
            ->willReturn($localResults);

        // Mock Guzzle client
        $clientMock = $this->createMock(Client::class);
        $promiseMock = new FulfilledPromise(new Response(200, [], json_encode(['results' => [], 'facets' => []])));

        $clientMock->expects($this->once())
            ->method('getAsync')
            ->willReturn($promiseMock);

        // Use reflection to inject the mock client
        $reflectedClass = new ReflectionClass(SearchService::class);
        $reflectedProperty = $reflectedClass->getProperty('client');
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($this->searchService, $clientMock);

        $elasticConfig['location'] = "https://example.com";
		$elasticConfig['key'] 	   = "dXNlcm5hbWU6cGFzc3dvcmQ=";
		$elasticConfig['index']    = "objects";

        $results = $this->searchService->search([], $elasticConfig, []);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('results', $results);
        $this->assertArrayHasKey('facets', $results);
    }
}
