<?php

namespace Service;

use PHPUnit\Framework\TestCase;
use OCA\OpenCatalogi\Service\ElasticSearchService;

class ElasticSearchServiceTest extends TestCase {
    private $mockClient;

    protected function setUp(): void {
        $this->mockClient = $this->createMock(ClientAdapter::class);
    }

    public function testAddObject() {
        $this->mockClient->expects($this->once())
            ->method('index')
            ->willReturn(['_index' => 'test-index', '_id' => '1', '_source' => ['field' => 'value']]);

        $service = new ElasticSearchService($this->mockClient);

        $config = [
            'location' => 'http://localhost:9200', // Replace with your Elasticsearch node location
            'key' => 'dGVzdDp0ZXN0', // Replace with your Elasticsearch API key
            'index' => 'test-index' // Replace with your Elasticsearch index
        ];

        $response = $service->addObject(['id' => '1', 'field' => 'value'], $config);
        $this->assertEquals(['field' => 'value'], $response);
    }

    public function testRemoveObject() {
        $this->mockClient->expects($this->once())
            ->method('delete')
            ->willReturn(['result' => 'deleted']);

        $service = new ElasticSearchService($this->mockClient);

        $config = [
            'location' => 'http://localhost:9200',

            'key' => 'dGVzdDp0ZXN0', // Replace with your Elasticsearch API key
            'index' => 'test-index'
        ];

        $response = $service->removeObject('1', $config);
        $this->assertEquals([], $response);
    }

    public function testUpdateObject() {
        $this->mockClient->expects($this->once())
            ->method('update')
            ->willReturn(['result' => 'updated']);

        $service = new ElasticSearchService($this->mockClient);

        $config = [
            'location' => 'http://localhost:9200',

            'key' => 'dGVzdDp0ZXN0', // Replace with your Elasticsearch API key
            'index' => 'test-index'
        ];

        $response = $service->updateObject('1', ['field' => 'new_value'], $config);
        $this->assertEquals([], $response);
    }

    public function testSearchObject() {
        $this->mockClient->expects($this->once())
            ->method('search')
            ->willReturn(['hits' => ['hits' => [['_source' => ['field' => 'value']]]]]);

        $service = new ElasticSearchService($this->mockClient);

        $config = [
            'location' => 'http://localhost:9200',

            'key' => 'dGVzdDp0ZXN0', // Replace with your Elasticsearch API key
            'index' => 'test-index'
        ];

        $response = $service->searchObject(['_search' => 'value'], $config);
        $this->assertEquals(['results' => [['field' => 'value']]], $response);
    }
}

class ClientAdapter {
    public function index(array $params) {}
    public function delete(array $params) {}
    public function update(array $params) {}
    public function search(array $params) {}
}
