<?php

namespace OCA\OpenCatalogi\Tests\Service;

use Elastic\Elasticsearch\Client;
use OCA\OpenCatalogi\Service\ElasticSearchClientAdapter;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ElasticSearchClientAdapterTest extends TestCase
{
    /** @var MockObject|Client */
    private $client;

    /** @var ElasticSearchClientAdapter */
    private $adapter;

    // protected function setUp(): void
    // {
    //     $this->client = $this->createMock(Client::class);
    //     $this->adapter = new ElasticSearchClientAdapter($this->client);
    // }

    // public function testSearch()
    // {
    //     $params = ['index' => 'test', 'body' => ['query' => ['match_all' => (object)[]]]];
    //     $expectedResult = ['hits' => ['total' => 1, 'hits' => []]];

    //     $this->client->expects($this->once())
    //                  ->method('search')
    //                  ->with($params)
    //                  ->willReturn($expectedResult);

    //     $result = $this->adapter->search($params);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function testIndex()
    // {
    //     $params = ['index' => 'test', 'id' => '1', 'body' => ['title' => 'Test']];
    //     $expectedResult = ['_id' => '1', 'result' => 'created'];

    //     $this->client->expects($this->once())
    //                  ->method('index')
    //                  ->with($params)
    //                  ->willReturn($expectedResult);

    //     $result = $this->adapter->index($params);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function testGet()
    // {
    //     $params = ['index' => 'test', 'id' => '1'];
    //     $expectedResult = ['_id' => '1', '_source' => ['title' => 'Test']];

    //     $this->client->expects($this->once())
    //                  ->method('get')
    //                  ->with($params)
    //                  ->willReturn($expectedResult);

    //     $result = $this->adapter->get($params);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function testUpdate()
    // {
    //     $params = ['index' => 'test', 'id' => '1', 'body' => ['doc' => ['title' => 'Updated Test']]];
    //     $expectedResult = ['_id' => '1', 'result' => 'updated'];

    //     $this->client->expects($this->once())
    //                  ->method('update')
    //                  ->with($params)
    //                  ->willReturn($expectedResult);

    //     $result = $this->adapter->update($params);
    //     $this->assertEquals($expectedResult, $result);
    // }

    // public function testDelete()
    // {
    //     $params = ['index' => 'test', 'id' => '1'];
    //     $expectedResult = ['_id' => '1', 'result' => 'deleted'];

    //     $this->client->expects($this->once())
    //                  ->method('delete')
    //                  ->with($params)
    //                  ->willReturn($expectedResult);

    //     $result = $this->adapter->delete($params);
    //     $this->assertEquals($expectedResult, $result);
    // }
}

?>
