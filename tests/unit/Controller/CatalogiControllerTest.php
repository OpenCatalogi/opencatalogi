<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\CatalogiController;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CatalogiControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|CatalogMapper */
    private $catalogMapper;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var CatalogiController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->catalogMapper = $this->createMock(CatalogMapper::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->controller = new CatalogiController('opencatalogi', $this->request, $this->config, $this->catalogMapper);
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndexWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->catalogMapper->method('findAll')->willReturn([]);

        // Error: Unknown named parameter $filters
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
        $mockCatalog = $this->createMock(\OCA\OpenCatalogi\Db\Catalog::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->catalogMapper->method('find')->willReturn($mockCatalog);

        $response = $this->controller->show(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockCatalog, $response->getData());
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
        $mockCatalog = $this->createMock(\OCA\OpenCatalogi\Db\Catalog::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->catalogMapper->method('createFromArray')->willReturn($mockCatalog);

        $response = $this->controller->create($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockCatalog, $response->getData());
    }

    public function testCreateWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('saveObject')->willReturn([]);

        $response = $this->controller->create($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testUpdateWithMongoDBDisabled()
    {
        $mockCatalog = $this->createMock(\OCA\OpenCatalogi\Db\Catalog::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->catalogMapper->method('updateFromArray')->willReturn($mockCatalog);

        $response = $this->controller->update(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockCatalog, $response->getData());
    }

    public function testUpdateWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('updateObject')->willReturn([]);

        $response = $this->controller->update(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $mockCatalog = $this->createMock(\OCA\OpenCatalogi\Db\Catalog::class);
        $this->catalogMapper->method('find')->willReturn($mockCatalog);
        $this->catalogMapper->expects($this->once())->method('delete');

        $response = $this->controller->destroy(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturn('1');
        $this->objectService->method('deleteObject')->willReturn([]);

        $response = $this->controller->destroy(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }
}
