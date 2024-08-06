<?php

namespace OCA\OpenCatalogi\Tests\Service;
//
// Niet te testen zonder code aan te passen. De code in Elastic\Elasticsearch\Client is declared as final. 
// Er zijn libs zoals Mockito etc om dit te doen, of Wrapper classes  

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Exception\ElasticsearchException;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ElasticSearchServiceTest extends TestCase
{
    /** @var MockObject|Client */
    private $client;

    /** @var ElasticSearchService */
    private $elasticSearchService;

    // protected function setUp(): void
    // {
    //     $this->client = $this->createMock(Client::class);
    //     $this->elasticSearchService = $this->getMockBuilder(ElasticSearchService::class)
    //         ->onlyMethods(['getClient'])
    //         ->getMock();
    //     $this->elasticSearchService->method('getClient')->willReturn($this->client);
    // }

    // public function testAddObject()
    // {
    //     $object = ['id' => '123', 'title' => 'Test Object'];
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $this->client->method('index')->willReturn(['_id' => '123']);
    //     $this->client->method('get')->willReturn(['_source' => $object]);

    //     $result = $this->elasticSearchService->addObject($object, $config);
    //     $this->assertEquals($object, $result);
    // }

    // public function testAddObjectException()
    // {
    //     $object = ['id' => '123', 'title' => 'Test Object'];
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $this->client->method('index')->willThrowException(new \Exception('Test Exception'));

    //     $result = $this->elasticSearchService->addObject($object, $config);
    //     $this->assertArrayHasKey('exception', $result);
    //     $this->assertEquals('Test Exception', $result['exception']['message']);
    // }

    // public function testRemoveObject()
    // {
    //     $id = '123';
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $this->client->method('delete')->willReturn([]);

    //     $result = $this->elasticSearchService->removeObject($id, $config);
    //     $this->assertEquals([], $result);
    // }

    // public function testRemoveObjectException()
    // {
    //     $id = '123';
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $this->client->method('delete')->willThrowException(new \Exception('Test Exception'));

    //     $result = $this->elasticSearchService->removeObject($id, $config);
    //     $this->assertArrayHasKey('exception', $result);
    //     $this->assertEquals('Test Exception', $result['exception']['message']);
    // }

    // public function testUpdateObject()
    // {
    //     $id = '123';
    //     $object = ['title' => 'Updated Object'];
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $this->client->method('index')->willReturn(['_id' => '123']);

    //     $result = $this->elasticSearchService->updateObject($id, $object, $config);
    //     $this->assertEquals([], $result);
    // }

    // public function testUpdateObjectException()
    // {
    //     $id = '123';
    //     $object = ['title' => 'Updated Object'];
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $this->client->method('index')->willThrowException(new \Exception('Test Exception'));

    //     $result = $this->elasticSearchService->updateObject($id, $object, $config);
    //     $this->assertArrayHasKey('exception', $result);
    //     $this->assertEquals('Test Exception', $result['exception']['message']);
    // }

    // public function testSearchObject()
    // {
    //     $filters = ['key' => 'value'];
    //     $config = ['index' => 'test_index', 'location' => 'http://localhost', 'key' => base64_encode('user:password')];

    //     $searchResults = [
    //         'hits' => [
    //             'hits' => [['_source' => ['id' => '123', 'title' => 'Test Object']]]
    //         ]
    //     ];

    //     $this->client->method('search')->willReturn($searchResults);

    //     $result = $this->elasticSearchService->searchObject($filters, $config);
    //     $this->assertArrayHasKey('results', $result);
    //     $this->assertEquals('Test Object', $result['results'][0]['title']);
    // }
}
