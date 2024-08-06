<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\MetaDataController;
use OCA\OpenCatalogi\Db\MetaDataMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MetaDataControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|MetaDataMapper */
    private $metaDataMapper;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var MetaDataController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->metaDataMapper = $this->createMock(MetaDataMapper::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->controller = new MetaDataController('opencatalogi', $this->request, $this->config, $this->metaDataMapper);
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndexWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->metaDataMapper->method('findAll')->willReturn([]);

        // REQUEST FOR CHANGE Unknowen parameter $filters
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
        $mockMetaData = $this->createMock(\OCA\OpenCatalogi\Db\MetaData::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->metaDataMapper->method('find')->willReturn($mockMetaData);

        $response = $this->controller->show(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockMetaData, $response->getData());
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
        $mockMetaData = $this->createMock(\OCA\OpenCatalogi\Db\MetaData::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->metaDataMapper->method('createFromArray')->willReturn($mockMetaData);

        $response = $this->controller->create($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockMetaData, $response->getData());
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
        $mockMetaData = $this->createMock(\OCA\OpenCatalogi\Db\MetaData::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->metaDataMapper->method('updateFromArray')->willReturn($mockMetaData);

        $response = $this->controller->update(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($mockMetaData, $response->getData());
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
        $mockMetaData = $this->createMock(\OCA\OpenCatalogi\Db\MetaData::class);
        $this->metaDataMapper->method('find')->willReturn($mockMetaData);
        $this->metaDataMapper->expects($this->once())->method('delete');

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
