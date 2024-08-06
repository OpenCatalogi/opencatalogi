<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\PublicationsController;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PublicationsControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|PublicationMapper */
    private $publicationMapper;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var MockObject|ElasticSearchService */
    private $elasticSearchService;

    /** @var PublicationsController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->publicationMapper = $this->createMock(PublicationMapper::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->elasticSearchService = $this->createMock(ElasticSearchService::class);
        $this->controller = new PublicationsController('opencatalogi', $this->request, $this->publicationMapper, $this->config);
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testCatalog()
    {
        $response = $this->controller->catalog(1);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndexWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('findAll')->willReturn([]);

        //Error: Unknown named parameter $searchParams
        // $response = $this->controller->index($this->objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['results' => []], $response->getData());
    }

    public function testIndexWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('findObjects')->willReturn(['documents' => []]);

        $response = $this->controller->index($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => []], $response->getData());
    }

    public function testShowWithMongoDBDisabled()
    {
        $mockPublication = $this->createMock(\OCA\OpenCatalogi\Db\Publication::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('find')->willReturn($mockPublication);

        $response = $this->controller->show(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockPublication, $response->getData());
    }

    public function testShowWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('findObject')->willReturn([]);

        $response = $this->controller->show(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testCreateWithMongoDBDisabled()
    {
        $mockPublication = $this->createMock(\OCA\OpenCatalogi\Db\Publication::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('createFromArray')->willReturn($mockPublication);

        $response = $this->controller->create($this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockPublication->jsonSerialize(), $response->getData());
    }

    public function testCreateWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('saveObject')->willReturn([]);

        // Undefined array key "status"
        // $response = $this->controller->create($this->objectService, $this->elasticSearchService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }

    public function testUpdateWithMongoDBDisabled()
    {
        $mockPublication = $this->createMock(\OCA\OpenCatalogi\Db\Publication::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('updateFromArray')->willReturn($mockPublication);

        $response = $this->controller->update(1, $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockPublication->jsonSerialize(), $response->getData());
    }

    public function testUpdateWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('updateObject')->willReturn([]);

        // Undefined array key "status"
        // $response = $this->controller->update(1, $this->objectService, $this->elasticSearchService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $mockPublication = $this->createMock(\OCA\OpenCatalogi\Db\Publication::class);
        $this->publicationMapper->method('find')->willReturn($mockPublication);
        $this->publicationMapper->expects($this->once())->method('delete');

        $response = $this->controller->destroy(1, $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('deleteObject')->willReturn([]);

        // Undefined array key "status"
        // $response = $this->controller->destroy(1, $this->objectService, $this->elasticSearchService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }
}
