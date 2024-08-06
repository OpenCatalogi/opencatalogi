<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\AttachmentsController;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class AttachmentsControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|AttachmentMapper */
    private $attachmentMapper;

    /** @var MockObject|FileService */
    private $fileService;

    /** @var AttachmentsController */
    private $controller;

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

    protected function createMockAttachment(): MockObject
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
        $objectService->method('findObjects')->willReturn(['documents' => []]);

        $response = $this->controller->index($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => []], $response->getData());
    }

    public function testShow()
    {
        $objectService = $this->createMock(ObjectService::class);
        $mockAttachment = $this->createMockAttachment();

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->attachmentMapper->method('find')->willReturn($mockAttachment);
        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->show('testId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
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

        $mockAttachment = $this->createMockAttachment();

        $this->attachmentMapper->method('find')->willReturn($mockAttachment);
        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->show('nonExistentId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
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
        $this->config->method('hasKey')->willReturn(false);

        $mockAttachment = $this->createMockAttachment();

        $this->attachmentMapper->method('createFromArray')->willReturn($mockAttachment);

        $response = $this->controller->create($objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
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
        $this->config->method('hasKey')->willReturn(false);

        $mockAttachment = $this->createMockAttachment();

        $this->attachmentMapper->method('createFromArray')->willReturn($mockAttachment);

        $response = $this->controller->create($objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
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
        $this->config->method('hasKey')->willReturn(false);

        $mockAttachment = $this->createMockAttachment();

        $this->attachmentMapper->method('updateFromArray')->willReturn($mockAttachment);

        $response = $this->controller->update('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
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
        $this->config->method('hasKey')->willReturn(false);

        $mockAttachment = $this->createMockAttachment();

        $this->attachmentMapper->method('updateFromArray')->willReturn($mockAttachment);

        $response = $this->controller->update('testId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockAttachment, $response->getData());
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

        $this->attachmentMapper->method('find')->willReturn($this->createMockAttachment());
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
        $this->config->method('hasKey')->willReturn(false);

        $mockAttachment = $this->createMockAttachment();

        $this->attachmentMapper->method('find')->willReturn($mockAttachment);

        $response = $this->controller->destroy('nonExistentId', $objectService, $elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    



    public function testPageWithNullParameter()
    {
        $response = $this->controller->page(null);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testCatalogWithStringId()
    {
        $response = $this->controller->catalog('stringId');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testCatalogWithIntId()
    {
        $response = $this->controller->catalog(123);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }



}
