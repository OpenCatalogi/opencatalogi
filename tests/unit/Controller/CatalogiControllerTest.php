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
use PHPUnit\Framework\TestCase;

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
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndexWithMongoDBDisabled()
    {
        $filters = ['_search' => 'test'];
        $this->request->method('getParams')->willReturn($filters);
        $this->config->method('hasKey')->willReturn(false);
        $this->config->method('getValueString')->willReturn('0');

        // Mock findAll to accept correct parameters format
        $this->catalogMapper->method('findAll')->willReturn([]);

        // $response = $this->controller->index($this->createMock(ObjectService::class));
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['results' => []], $response->getData());
    }

    public function testIndexWithMongoDBEnabled()
    {
        $filters = ['_search' => 'test'];
        $this->request->method('getParams')->willReturn($filters);
        $this->config->method('hasKey')->willReturn(true);

        // Ensure getValueString returns valid strings
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '', '1'],
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService = $this->createMock(ObjectService::class);
        $objectService->method('findObjects')->willReturn(['documents' => []]);

        // $response = $this->controller->index($objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['results' => []], $response->getData());
    }

    public function testShowWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->config->method('getValueString')->willReturn('0');

        $catalog = new Catalog();
        $catalog->setId(1);
        $catalog->setTitle('Test Catalog');

        $this->catalogMapper->method('find')->willReturn($catalog);

        $response = $this->controller->show(1, $this->createMock(ObjectService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($catalog, $response->getData());
    }

    public function testShowWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);

        // Ensure getValueString returns valid strings
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '', '1'],
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService = $this->createMock(ObjectService::class);
        $objectService->method('findObject')->willReturn(['_id' => '1', 'title' => 'Test Catalog']);

    //     $response = $this->controller->show(1, $objectService);
    //     $this->assertInstanceOf(JSONResponse::class, $response);
    //     $this->assertEquals(['_id' => '1', 'title' => 'Test Catalog'], $response->getData());
    }

    public function testShowWithInvalidId()
    {
        $this->config->method('hasKey')->willReturn(true);

        // Ensure getValueString returns valid strings
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '', '1'],
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService = $this->createMock(ObjectService::class);
        $objectService->method('findObject')->willReturn([]);

        // $response = $this->controller->show('invalidId', $objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }

    public function testCreateWithMongoDBDisabled()
    {
        $data = ['title' => 'New Catalog'];
        $this->request->method('getParams')->willReturn($data);
        $this->config->method('hasKey')->willReturn(false);
        $this->config->method('getValueString')->willReturn('0');

        $catalog = new Catalog();
        $catalog->setId(1);
        $catalog->setTitle($data['title']);

        $this->catalogMapper->method('createFromArray')->willReturn($catalog);

        $response = $this->controller->create($this->createMock(ObjectService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($catalog, $response->getData());
    }

    public function testCreateWithMongoDBEnabled()
    {
        $data = ['title' => 'New Catalog'];
        $this->request->method('getParams')->willReturn($data);
        $this->config->method('hasKey')->willReturn(true);

        // Ensure getValueString returns valid strings
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '', '1'],
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService = $this->createMock(ObjectService::class);
        $objectService->method('saveObject')->willReturn($data);

        // $response = $this->controller->create($objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals($data, $response->getData());
    }

    public function testUpdateWithMongoDBDisabled()
    {
        $data = ['title' => 'Updated Catalog'];
        $this->request->method('getParams')->willReturn($data);
        $this->config->method('hasKey')->willReturn(false);
        $this->config->method('getValueString')->willReturn('0');

        $catalog = new Catalog();
        $catalog->setId(1);
        $catalog->setTitle($data['title']);

        $this->catalogMapper->method('updateFromArray')->willReturn($catalog);

        $response = $this->controller->update(1, $this->createMock(ObjectService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($catalog, $response->getData());
    }

    public function testUpdateWithMongoDBEnabled()
    {
        $data = ['title' => 'Updated Catalog'];
        $this->request->method('getParams')->willReturn($data);
        $this->config->method('hasKey')->willReturn(true);

        // Ensure getValueString returns valid strings
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '', '1'],
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService = $this->createMock(ObjectService::class);
        $objectService->method('updateObject')->willReturn($data);

        // $response = $this->controller->update(1, $objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
    //     $this->assertEquals($data, $response->getData());
    }

    public function testDeleteWithMongoDBDisabled()
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->config->method('getValueString')->willReturn('0');

        $catalog = new Catalog();
        $catalog->setId(1);
        $catalog->setTitle('Test Catalog');

        $this->catalogMapper->method('find')->willReturn($catalog);
        $this->catalogMapper->method('delete')->willReturn($this->createMock(\OCP\AppFramework\Db\Entity::class));

        $response = $this->controller->destroy(1, $this->createMock(ObjectService::class));
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDeleteWithMongoDBEnabled()
    {
        $this->config->method('hasKey')->willReturn(true);

        // Ensure getValueString returns valid strings
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '', '1'],
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService = $this->createMock(ObjectService::class);
        $objectService->method('deleteObject')->willReturn([]);

        // $response = $this->controller->destroy(1, $objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }
}
