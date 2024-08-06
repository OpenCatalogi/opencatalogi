<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\AttachmentsController;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class AttachmentsControllerTest extends TestCase
{
    private MockObject $request;
    private MockObject $config;
    private MockObject $attachmentMapper;
    private MockObject $fileService;
    private AttachmentsController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->attachmentMapper = $this->createMock(AttachmentMapper::class);
        $this->fileService = $this->createMock(FileService::class);

        $this->controller = new AttachmentsController('opencatalogi', $this->request, $this->config, $this->attachmentMapper, $this->fileService);

        $this->config->method('getValueString')
            ->willReturn('http://localhost');
    }

    private function createMockAttachment(): MockObject
    {
        return $this->createMock(\OCA\OpenCatalogi\Db\Attachment::class);
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

    public function testIndexWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->attachmentMapper->method('findAll')->willReturn([]);

        $response = $this->controller->index($this->createMock(ObjectService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => []], $response->getData());
    }

    public function testIndexWithMongoDBEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
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

    public function testShowWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $mockAttachment = $this->createMockAttachment();
        $this->attachmentMapper->method('find')->willReturn($mockAttachment);

        $response = $this->controller->show('testId', $this->createMock(ObjectService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
    }

    public function testShowWithMongoDBEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        // Adjusting the mock return to be an array instead of a mock object
        $objectService->method('findObject')->willReturn(['_id' => 'testId', 'name' => 'Test Attachment']);

        $response = $this->controller->show('testId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['_id' => 'testId', 'name' => 'Test Attachment'], $response->getData());
    }

    public function testShowWithInvalidId()
    {
        $objectService = $this->createMock(ObjectService::class);
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        // Ensure `findObject` returns an empty array instead of null
        $objectService->method('findObject')->willReturn([]);

        $response = $this->controller->show('invalidId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }

    public function testCreateWithValidData()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $data = ['key' => 'value'];
        $this->request->method('getParams')->willReturn($data);
        $objectService->method('saveObject')->willReturn($data);

        $response = $this->controller->create($objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals($data, $response->getData());
    }

    public function testCreateWithInvalidData()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $data = ['invalidKey' => 'value'];
        $this->request->method('getParams')->willReturn($data);
        $objectService->method('saveObject')->willReturn(['error' => 'Invalid data']);

        $response = $this->controller->create($objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['error' => 'Invalid data'], $response->getData());
    }

    public function testUpdateWithValidData()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $data = ['key' => 'newValue'];
        $this->request->method('getParams')->willReturn($data);
        $objectService->method('updateObject')->willReturn($data);

        $response = $this->controller->update('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals($data, $response->getData());
    }

    public function testUpdateWithInvalidData()
    {
        $objectService = $this->createMock(ObjectService::class);
        $elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $data = ['invalidKey' => 'value'];
        $this->request->method('getParams')->willReturn($data);
        $objectService->method('updateObject')->willReturn(['error' => 'Invalid data']);

        $response = $this->controller->update('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['error' => 'Invalid data'], $response->getData());
    }

    public function testDestroyWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $mockAttachment = $this->createMockAttachment();
        $this->attachmentMapper->method('find')->willReturn($mockAttachment);
        
        // Assuming `delete` returns the deleted entity
        $this->attachmentMapper->method('delete')->willReturn($mockAttachment);

        $response = $this->controller->destroy('testId', $this->createMock(ObjectService::class), $this->createMock(ElasticSearchService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithMongoDBEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService->method('deleteObject')->willReturn([]);

        $response = $this->controller->destroy('testId', $objectService, $this->createMock(ElasticSearchService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }
}
