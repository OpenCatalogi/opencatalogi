<?php

use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\Promise;
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
        $this->searchService = new SearchService($this->objectServiceMock);
    }

    public function testSortResultArray()
    {
        $a = ['_score' => 1];
        $b = ['_score' => 2];
        $this->assertEquals(-1, $this->searchService->sortResultArray($a, $b));
        $this->assertEquals(1, $this->searchService->sortResultArray($b, $a));
        $this->assertEquals(0, $this->searchService->sortResultArray($a, $a));
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
        $promiseMock = $this->createMock(Promise::class);

        $clientMock->expects($this->once())
            ->method('getAsync')
            ->willReturn($promiseMock);

        $promiseMock->method('wait')
            ->willReturn(new Response(200, [], json_encode(['results' => [], 'facets' => []])));

        // Use reflection to inject the mock client
        $reflectedClass = new \ReflectionClass(SearchService::class);
        $reflectedProperty = $reflectedClass->getProperty('getClient');
        $reflectedProperty->setAccessible(true);
        $reflectedProperty->setValue($this->searchService, $clientMock);

        $results = $this->searchService->search([], [], []);

        $this->assertIsArray($results);
        $this->assertArrayHasKey('results', $results);
        $this->assertArrayHasKey('facets', $results);
    }
}