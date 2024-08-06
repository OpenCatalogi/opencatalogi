<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\CatalogiController;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\Catalog;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;
use Test\TestCase;

class CatalogiControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|CatalogMapper */
    private $catalogMapper;

    /** @var CatalogiController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->catalogMapper = $this->createMock(CatalogMapper::class);
        $this->controller = new CatalogiController('opencatalogi', $this->request, $this->config, $this->catalogMapper);

        // Ensure the config mock always returns a string
        $this->config->method('getValueString')
            ->willReturn('http://localhost');
    }

    protected function createMockCatalog(array $data = []): MockObject
    {
        $catalog = $this->createMock(Catalog::class);
        $catalog->method('jsonSerialize')->willReturn($data);
        return $catalog;
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
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

        $this->catalogMapper->method('findAll')
            ->willReturn([['key' => 'value']]);

        $response = $this->controller->index($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => [['key' => 'value']]], $response->getData());
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

        $this->request->method('getParams')->willReturn(['key' => 'value']);
        $objectService->method('findObjects')->willThrowException(new \Exception('Database error'));

        $response = $this->controller->index($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertArrayHasKey('error', $response->getData());
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

        $mockCatalog = $this->createMockCatalog(['key' => 'value']);
        $this->catalogMapper->method('find')->willReturn($mockCatalog);

        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->show('testId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['key' => 'value'], $response->getData());
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

        $objectService->method('findObject')->willThrowException(new \Exception('Object not found'));

        $response = $this->controller->show('invalidId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $data = $response->getData();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Object not found', $data['error']);
    }

    public function testCreate()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'value']);
        $objectService->method('saveObject')->willReturn(['key' => 'value']);

        $mockCatalog = $this->createMockCatalog(['key' => 'value']);
        $this->catalogMapper->method('createFromArray')->willReturn($mockCatalog);

        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->create($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['key' => 'value'], $response->getData());
    }

    public function testCreateWithError()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'value']);
        $objectService->method('saveObject')->willThrowException(new \Exception('Save failed'));

        $mockCatalog = $this->createMockCatalog(['key' => 'value']);
        $this->catalogMapper->method('createFromArray')->willReturn($mockCatalog);

        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->create($objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $data = $response->getData();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Save failed', $data['error']);
    }

    public function testUpdate()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'newValue']);
        $objectService->method('updateObject')->willReturn(['key' => 'newValue']);

        $mockCatalog = $this->createMockCatalog(['key' => 'newValue']);
        $this->catalogMapper->method('updateFromArray')->willReturn($mockCatalog);

        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->update('testId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['key' => 'newValue'], $response->getData());
    }

    public function testUpdateWithError()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->request->method('getParams')->willReturn(['key' => 'newValue']);
        $objectService->method('updateObject')->willThrowException(new \Exception('Update failed'));

        $mockCatalog = $this->createMockCatalog(['key' => 'newValue']);
        $this->catalogMapper->method('updateFromArray')->willReturn($mockCatalog);

        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->update('invalidId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $data = $response->getData();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Update failed', $data['error']);
    }

    public function testDestroy()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster']
            ]);

        $this->catalogMapper->method('find')->willReturn($this->createMockCatalog());
        $objectService->method('deleteObject')->willReturn([]);

        $this->config->method('hasKey')->willReturn(false);

        $response = $this->controller->destroy('testId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithError()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('getValueString')
            ->willReturnMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
                ['opencatalogi', 'mongodbKey', '', 'someKey'],
                ['opencatalogi', 'mongodbCluster', '', 'someCluster'],
                ['opencatalogi', 'mongoStorage', '', '1']
            ]);

        $this->config->method('hasKey')->willReturn(true);

        // Simulate an exception being thrown by deleteObject
        $objectService->method('deleteObject')->willThrowException(new \Exception('Delete failed'));

        $response = $this->controller->destroy('invalidId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);

        $data = $response->getData();
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Delete failed', $data['error']);
    }
}
