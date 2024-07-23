<?php

use PHPUnit\Framework\TestCase;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\ElasticSearchClientAdapter;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticSearchServiceTest extends TestCase
{
    public function testGetClient()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret')
        ];

        // Create an instance of the real client
        $client = ClientBuilder::create()->setHosts(['http://localhost:9200'])->build();

        // Create an adapter with the real client
        $clientAdapter = new ElasticSearchClientAdapter($client);

        // Create an instance of ElasticSearchService
        $elasticSearchService = new ElasticSearchService($clientAdapter);

        // Use reflection to access the private method
        $reflection = new ReflectionClass(ElasticSearchService::class);
        $method = $reflection->getMethod('getClient');
        $method->setAccessible(true);

        // Call the private method
        $clientAdapterFromMethod = $method->invokeArgs($elasticSearchService, [$config]);

        // Assert that the client is an instance of OCA\OpenCatalogi\Service\ElasticSearchClientAdapter
        $this->assertInstanceOf(ElasticSearchClientAdapter::class, $clientAdapterFromMethod);
    }

    public function testAddObjectSuccess()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the object to add
        $object = [
            'id' => 'test_id',
            'name' => 'test_name'
        ];

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the index method
        $clientAdapterMock->expects($this->once())
            ->method('index')
            ->with($this->arrayHasKey('index'))
            ->willReturn(['result' => 'created']);

        // Set up the expectations for the get method
        $clientAdapterMock->expects($this->once())
            ->method('get')
            ->with($this->arrayHasKey('index'))
            ->willReturn(['_source' => $object]);

        // Create an instance of ElasticSearchService with the mocked client adapter
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Call the addObject method
        $result = $elasticSearchService->addObject($object, $config);

        // Assert that the result matches the expected object
        $this->assertEquals($object, $result);
    }

    public function testAddObjectException()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the object to add
        $object = [
            'id' => 'test_id',
            'name' => 'test_name'
        ];

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the index method to throw an exception
        $clientAdapterMock->expects($this->once())
            ->method('index')
            ->will($this->throwException(new \Exception('Test exception')));

        // Create an instance of ElasticSearchService with the mocked client adapter
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Call the addObject method
        $result = $elasticSearchService->addObject($object, $config);

        // Assert that the result contains the exception information
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals('Test exception', $result['exception']['message']);
    }

    public function testRemoveObjectSuccess()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the ID to remove
        $id = 'test_id';

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the delete method
        $clientAdapterMock->expects($this->once())
            ->method('delete')
            ->with($this->arrayHasKey('index'))
            ->willReturn([]);

        // Create an instance of ElasticSearchService with the mocked client adapter
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Call the removeObject method
        $result = $elasticSearchService->removeObject($id, $config);

        // Assert that the result is an empty array
        $this->assertEquals([], $result);
    }

    public function testRemoveObjectException()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the ID to remove
        $id = 'test_id';

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the delete method to throw an exception
        $clientAdapterMock->expects($this->once())
            ->method('delete')
            ->will($this->throwException(new \Exception('Test exception')));

        // Create an instance of ElasticSearchService with the mocked client adapter
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Call the removeObject method
        $result = $elasticSearchService->removeObject($id, $config);

        // Assert that the result contains the exception information
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals('Test exception', $result['exception']['message']);
    }

    public function testUpdateObjectSuccess()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the object to update
        $object = [
            'name' => 'updated_name'
        ];

        // Mock the ID to update
        $id = 'test_id';

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the update method
        $clientAdapterMock->expects($this->once())
            ->method('update')
            ->with($this->arrayHasKey('index'))
            ->willReturn([]);

        // Create an instance of ElasticSearchService with the mocked client adapter
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Call the updateObject method
        $result = $elasticSearchService->updateObject($id, $object, $config);

        // Assert that the result is an empty array
        $this->assertEquals([], $result);
    }

    public function testUpdateObjectException()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the object to update
        $object = [
            'name' => 'updated_name'
        ];

        // Mock the ID to update
        $id = 'test_id';

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the update method to throw an exception
        $clientAdapterMock->expects($this->once())
            ->method('update')
            ->will($this->throwException(new \Exception('Test exception')));

        // Create an instance of ElasticSearchService with the mocked client adapter
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Call the updateObject method
        $result = $elasticSearchService->updateObject($id, $object, $config);

        // Assert that the result contains the exception information
        $this->assertArrayHasKey('exception', $result);
        $this->assertEquals('Test exception', $result['exception']['message']);
    }

    public function testParseFilters()
    {
        // Create an instance of ElasticSearchService
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Use reflection to access the private method
        $reflection = new ReflectionClass(ElasticSearchService::class);
        $method = $reflection->getMethod('parseFilters');
        $method->setAccessible(true);

        // Define test filters
        $filters = [
            '_search' => 'test',
            '_queries' => ['field1', 'field2'],
            'field3' => 'value3',
            'field4' => 'value4'
        ];

        // Expected parsed body
        $expectedBody = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['query_string' => ['query' => '*test*']],
                        ['match' => ['field3' => 'value3']],
                        ['match' => ['field4' => 'value4']]
                    ]
                ]
            ],
            'runtime_mappings' => [
                'field1' => ['type' => 'keyword'],
                'field2' => ['type' => 'keyword']
            ],
            'aggs' => [
                'field1' => ['terms' => ['field' => 'field1']],
                'field2' => ['terms' => ['field' => 'field2']]
            ]
        ];

        // Call the private method
        $parsedBody = $method->invokeArgs($elasticSearchService, [$filters]);

        // Assert that the parsed body matches the expected body
        $this->assertEquals($expectedBody, $parsedBody);
    }

    public function testFormatResults()
    {
        // Create an instance of ElasticSearchService
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Use reflection to access the private method
        $reflection = new ReflectionClass(ElasticSearchService::class);
        $method = $reflection->getMethod('formatResults');
        $method->setAccessible(true);

        // Define a test hit
        $hit = [
            '_source' => [
                'field1' => 'value1',
                'field2' => 'value2'
            ],
            '_id' => 'test_id',
            '_index' => 'test_index'
        ];

        // Expected formatted hit
        $expectedFormattedHit = [
            '_id' => 'test_id',
            '_index' => 'test_index',
            'field1' => 'value1',
            'field2' => 'value2'
        ];

        // Call the private method
        $formattedHit = $method->invokeArgs($elasticSearchService, [$hit]);

        // Assert that the formatted hit matches the expected formatted hit
        $this->assertEquals($expectedFormattedHit, $formattedHit);
    }

    public function testRenameBucketItems()
    {
        // Create an instance of ElasticSearchService
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Use reflection to access the private method
        $reflection = new ReflectionClass(ElasticSearchService::class);
        $method = $reflection->getMethod('renameBucketItems');
        $method->setAccessible(true);

        // Define a test bucket item
        $bucketItem = [
            'key' => 'test_key',
            'doc_count' => 42
        ];

        // Expected renamed bucket item
        $expectedRenamedBucketItem = [
            '_id' => 'test_key',
            'count' => 42
        ];

        // Call the private method
        $renamedBucketItem = $method->invokeArgs($elasticSearchService, [$bucketItem]);

        // Assert that the renamed bucket item matches the expected renamed bucket item
        $this->assertEquals($expectedRenamedBucketItem, $renamedBucketItem);
    }

    public function testMapAggregationResults()
    {
        // Create an instance of ElasticSearchService
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);
        $elasticSearchService = new ElasticSearchService($clientAdapterMock);

        // Use reflection to access the private method
        $reflection = new ReflectionClass(ElasticSearchService::class);
        $method = $reflection->getMethod('mapAggregationResults');
        $method->setAccessible(true);

        // Define a test aggregation result
        $aggregationResult = [
            'buckets' => [
                [
                    'key' => 'test_key_1',
                    'doc_count' => 42
                ],
                [
                    'key' => 'test_key_2',
                    'doc_count' => 35
                ]
            ]
        ];

        // Expected mapped aggregation result
        $expectedMappedAggregationResult = [
            [
                '_id' => 'test_key_1',
                'count' => 42
            ],
            [
                '_id' => 'test_key_2',
                'count' => 35
            ]
        ];

        // Call the private method
        $mappedAggregationResult = $method->invokeArgs($elasticSearchService, [$aggregationResult]);

        // Assert that the mapped aggregation result matches the expected mapped aggregation result
        $this->assertEquals($expectedMappedAggregationResult, $mappedAggregationResult);
    }

    public function testSearchObject()
    {
        // Mock the configuration array
        $config = [
            'location' => 'http://localhost:9200',
            'key' => base64_encode('apiKeyID:apiKeySecret'),
            'index' => 'test_index'
        ];

        // Mock the filters
        $filters = [
            '_search' => 'test_search',
            'field1' => 'value1'
        ];

        // Define the expected body after parsing filters
        $parsedBody = [
            'query' => [
                'bool' => [
                    'must' => [
                        ['query_string' => ['query' => '*test_search*']],
                        ['match' => ['field1' => 'value1']]
                    ]
                ]
            ]
        ];

        // Mock the search result from Elasticsearch
        $searchResult = [
            'hits' => [
                'hits' => [
                    [
                        '_source' => [
                            'field1' => 'value1',
                            'field2' => 'value2'
                        ],
                        '_id' => 'test_id_1',
                        '_index' => 'test_index'
                    ]
                ]
            ],
            'aggregations' => [
                'agg1' => [
                    'buckets' => [
                        ['key' => 'bucket_key_1', 'doc_count' => 10]
                    ]
                ]
            ]
        ];

        // Expected formatted search result
        $expectedSearchResult = [
            'results' => [
                [
                    '_id' => 'test_id_1',
                    '_index' => 'test_index',
                    'field1' => 'value1',
                    'field2' => 'value2'
                ]
            ],
            'facets' => [
                'agg1' => [
                    [
                        '_id' => 'bucket_key_1',
                        'count' => 10
                    ]
                ]
            ]
        ];

        // Mock the ElasticSearchClientAdapter class
        $clientAdapterMock = $this->createMock(ElasticSearchClientAdapter::class);

        // Set up the expectations for the search method
        $clientAdapterMock->expects($this->once())
            ->method('search')
            ->with($this->arrayHasKey('index'))
            ->willReturn($searchResult);

        // Create a partial mock of ElasticSearchService to mock the private methods
        $elasticSearchService = $this->getMockBuilder(ElasticSearchService::class)
                                    ->setConstructorArgs([$clientAdapterMock])
                                    ->onlyMethods(['parseFilters', 'formatResults', 'mapAggregationResults'])
                                    ->getMock();

        // Configure the parseFilters method to return the parsed body
        $elasticSearchService->method('parseFilters')->willReturn($parsedBody);

        // Configure the formatResults method to format the hits
        $elasticSearchService->method('formatResults')->willReturn([
            '_id' => 'test_id_1',
            '_index' => 'test_index',
            'field1' => 'value1',
            'field2' => 'value2'
        ]);

        // Configure the mapAggregationResults method to map the aggregation results
        $elasticSearchService->method('mapAggregationResults')->willReturn([
            [
                '_id' => 'bucket_key_1',
                'count' => 10
            ]
        ]);

        // Call the searchObject method
        $result = $elasticSearchService->searchObject($filters, $config);

        // Assert that the result matches the expected search result
        $this->assertEquals($expectedSearchResult, $result);
    }
}