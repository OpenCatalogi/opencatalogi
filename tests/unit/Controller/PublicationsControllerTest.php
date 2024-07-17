<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use Test\TestCase; 
use OCA\OpenCatalogi\Controller\PublicationsController;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;

class PublicationsControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var MockObject|ElasticSearchService */
    private $elasticSearchService;

    /** @var PublicationsController */
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->controller = new PublicationsController(
            'opencatalogi',
            $this->request,
            $this->config
        );
    }

    public function testPage()
    {
        $response = $this->controller->page(null);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndex()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('findObjects')->willReturn(['documents' => []]);

        $response = $this->controller->index($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testShow()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('findObject')->willReturn([]);

        $response = $this->controller->show('some-id', $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testCreate()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('saveObject')->willReturn(['id' => 'some-id']);
        $this->elasticSearchService->method('addObject')->willReturn(['id' => 'some-id']);

        $this->request->method('getParams')->willReturn(['_schema' => 'publication']);

        $response = $this->controller->create($this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testUpdate()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('updateObject')->willReturn(['id' => 'some-id']);
        $this->elasticSearchService->method('updateObject')->willReturn(['id' => 'some-id']);

        $this->request->method('getParams')->willReturn(['_schema' => 'publication']);

        $response = $this->controller->update('some-id', $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testDestroy()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('deleteObject')->willReturn([]);
        $this->elasticSearchService->method('removeObject')->willReturn([]);

        $response = $this->controller->destroy('some-id', $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    // Unhappy flow tests

    public function testShowWithNonExistentId()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('findObject')->willReturn([]);

        $response = $this->controller->show('non-existent-id', $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testCreateWithInvalidData()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('saveObject')->willReturn([]);

        $this->request->method('getParams')->willReturn([]);

        $response = $this->controller->create($this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testUpdateWithNonExistentId()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('updateObject')->willReturn([]);

        $this->request->method('getParams')->willReturn([]);

        $response = $this->controller->update('non-existent-id', $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testDestroyWithNonExistentId()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->objectService->method('deleteObject')->willReturn([]);

        $response = $this->controller->destroy('non-existent-id', $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }
}
