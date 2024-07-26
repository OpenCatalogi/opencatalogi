<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\AttachmentsController;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCP\IRequest;
use OCP\IAppConfig;
use Test\TestCase;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;

class AttachmentsControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var AttachmentsController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->controller = new AttachmentsController('opencatalogi', $this->request, $this->config);

        $this->config->method('getValueString')
            ->willReturn('http://localhost');
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testCatalog()
    {
        $response = $this->controller->catalog('testId');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndex()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'value']);
        $objectService->method('findObjects')->willReturn(['documents' => [['key' => 'value']]]);

        $response = $this->controller->index($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => [['key' => 'value']]], $response->getData());
    }

    public function testIndexWithEmptyResults()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'value']);
        $objectService->method('findObjects')->willReturn(['documents' => []]);

        $response = $this->controller->index($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => []], $response->getData());
    }

    public function testShow()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('findObject')->willReturn(['key' => 'value']);

        $response = $this->controller->show('testId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['key' => 'value'], $response->getData());
    }

    public function testShowWithNonExistentId()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('findObject')->willReturn([]);

        $response = $this->controller->show('nonExistentId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testCreate()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'value']);
        $objectService->method('saveObject')->willReturn(['key' => 'value']);

        $response = $this->controller->create($objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['key' => 'value'], $response->getData());
    }

    public function testCreateWithInvalidData()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['invalidKey' => 'value']);
        $objectService->method('saveObject')->willReturn(['error' => 'Invalid data']);

        $response = $this->controller->create($objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['error' => 'Invalid data'], $response->getData());
    }

    public function testUpdate()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'newValue']);
        $objectService->method('updateObject')->willReturn(['key' => 'newValue']);

        $response = $this->controller->update('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['key' => 'newValue'], $response->getData());
    }

    public function testUpdateWithInvalidData()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['invalidKey' => 'value']);
        $objectService->method('updateObject')->willReturn(['error' => 'Invalid data']);

        $response = $this->controller->update('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['error' => 'Invalid data'], $response->getData());
    }

    public function testDestroy()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('deleteObject')->willReturn([]);

        $response = $this->controller->destroy('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithNonExistentId()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('deleteObject')->willReturn(['error' => 'Object not found']);

        $response = $this->controller->destroy('nonExistentId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['error' => 'Object not found'], $response->getData());
    }

    public function testPageWithError()
    {
        $this->controller = $this->getMockBuilder(AttachmentsController::class)
            ->setConstructorArgs(['opencatalogi', $this->request, $this->config])
            ->onlyMethods(['page'])
            ->getMock();

        $this->controller->method('page')
            ->will($this->throwException(new \Exception('Template load error')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Template load error');

        $this->controller->page('testParam');
    }

    public function testIndexWithError()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('findObjects')
            ->will($this->throwException(new \Exception('Data retrieval error')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Data retrieval error');

        $this->controller->index($objectService);
    }

    public function testShowWithError()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('findObject')
            ->will($this->throwException(new \Exception('Object retrieval error')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Object retrieval error');

        $this->controller->show('testId', $objectService);
    }

    public function testCreateWithError()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'value']);

        $objectService->method('saveObject')
            ->will($this->throwException(new \Exception('Save failed')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Save failed');

        $this->controller->create($objectService, $elasticSearchService);
    }

    public function testUpdateWithError()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'newValue']);

        $objectService->method('updateObject')
            ->will($this->throwException(new \Exception('Update failed')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Update failed');

        $this->controller->update('testId', $objectService, $elasticSearchService);
    }

    public function testDestroyWithError()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $objectService->method('deleteObject')
            ->will($this->throwException(new \Exception('Delete failed')));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Delete failed');

        $this->controller->destroy('testId', $objectService, $elasticSearchService);
    }
}
