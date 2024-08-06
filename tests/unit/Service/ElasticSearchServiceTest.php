<?php

namespace OCA\OpenCatalogi\Tests\Service;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ElasticSearchServiceTest extends TestCase
{
    /** @var MockObject|Client */
    private $client;

    /** @var ElasticSearchService */
    private $service;

    // protected function setUp(): void
    // {
    //     $this->client = $this->createMock(Client::class);
    //     $this->service = $this->getMockBuilder(ElasticSearchService::class)
    //                           ->onlyMethods(['getClient'])
    //                           ->getMock();

    //     $this->service->method('getClient')
    //                   ->willReturn($this->client);
    // }

    // public function testAddObject()
    // {
    //     $config = ['location' => 'http://localhost', 'key' => base64_encode('id:key'), 'index' => 'test'];
    //     $object = ['id' => '1', 'title' => 'Test Object'];

    //     $this->client->method('index')
    //                  ->willReturn(['_source' => $object]);

    //     $result = $this->service->addObject($object, $config);
    //     $this->assertEquals($object, $result);
    // }

    // public function testRemoveObject()
    // {
    //     $config = ['location' => 'http://localhost', 'key' => base64_encode('id:key'), 'index' => 'test'];
    //     $id = '1';

    //     $this->client->expects($this->once())
    //                  ->method('delete')
    //                  ->with(['index' => 'test', 'id' => $id])
    //                  ->willReturn([]);

    //     $result = $this->service->removeObject($id, $config);
    //     $this->assertEquals([], $result);
    // }

    // public function testUpdateObject()
    // {
    //     $config = ['location' => 'http://localhost', 'key' => base64_encode('id:key'), 'index' => 'test'];
    //     $id = '1';
    //     $object = ['title' => 'Updated Object'];

    //     $this->client->method('index')
    //                  ->willReturn([]);

    //     $result = $this->service->updateObject($id, $object, $config);
    //     $this->assertEquals([], $result);
    // }

    // public function testSearchObject()
    // {
    //     $config = ['location' => 'http://localhost', 'key' => base64_encode('id:key'), 'index' => 'test'];
    //     $filters = ['title' => 'Test'];

    //     $response = [
    //         'hits' => ['hits' => [['_source' => ['title' => 'Test']]]],
    //         'aggregations' => []
    //     ];

    //     $this->client->method('search')
    //                  ->willReturn($response);

    //     $result = $this->service->searchObject($filters, $config);
    //     $expected = ['results' => [['title' => 'Test']], 'facets' => []];

    //     $this->assertEquals($expected, $result);
    // }

    // public function testParseFilters()
    // {
    //     $filters = [
    //         '.search' => 'test',
    //         '.queries' => ['query1', 'query2'],
    //         '.catalogi' => ['cat1', 'cat2'],
    //         'field1' => 'value1'
    //     ];

    //     $expected = [
    //         'query' => [
    //             'bool' => [
    //                 'must' => [
    //                     ['query_string' => ['query' => '*test*']],
    //                     ['match' => ['catalogi._id' => ['query' => 'cat1 cat2', 'operator' => 'OR']]],
    //                     ['match' => ['field1' => 'value1']]
    //                 ]
    //             ]
    //         ],
    //         'runtime_mappings' => [
    //             'query1' => ['type' => 'keyword'],
    //             'query2' => ['type' => 'keyword']
    //         ],
    //         'aggs' => [
    //             'query1' => ['terms' => ['field' => 'query1']],
    //             'query2' => ['terms' => ['field' => 'query2']]
    //         ]
    //     ];

    //     $result = $this->service->parseFilters($filters);
    //     $this->assertEquals($expected, $result);
    // }

    // public function testFormatResults()
    // {
    //     $hit = [
    //         '_source' => ['title' => 'Test'],
    //         '_id' => '1'
    //     ];

    //     $expected = [
    //         '_id' => '1',
    //         'title' => 'Test'
    //     ];

    //     $result = $this->service->formatResults($hit);
    //     $this->assertEquals($expected, $result);
    // }

    // public function testRenameBucketItems()
    // {
    //     $bucketItem = [
    //         'key' => 'bucket1',
    //         'doc_count' => 10
    //     ];

    //     $expected = [
    //         '_id' => 'bucket1',
    //         'count' => 10
    //     ];

    //     $result = $this->service->renameBucketItems($bucketItem);
    //     $this->assertEquals($expected, $result);
    // }

    // public function testMapAggregationResults()
    // {
    //     $result = [
    //         'buckets' => [
    //             ['key' => 'bucket1', 'doc_count' => 10],
    //             ['key' => 'bucket2', 'doc_count' => 5]
    //         ]
    //     ];

    //     $expected = [
    //         ['_id' => 'bucket1', 'count' => 10],
    //         ['_id' => 'bucket2', 'count' => 5]
    //     ];

    //     $mappedResult = $this->service->mapAggregationResults($result);
    //     $this->assertEquals($expected, $mappedResult);
    // }
}

?>
