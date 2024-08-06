<?php

namespace OCA\OpenCatalogi\Tests\Controller;

// Stelt dat de index() DirectoryController de filters niet goed doorgeeft? 


use OCA\OpenCatalogi\Controller\DirectoryController;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DirectoryControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|ListingMapper */
    private $listingMapper;

    /** @var DirectoryController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->listingMapper = $this->createMock(ListingMapper::class);
        $this->controller = new DirectoryController('opencatalogi', $this->request, $this->config, $this->listingMapper);

        $this->config->method('getValueString')
            ->willReturn('http://localhost');
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndexWithMongoStorageDisabled()
    {
        $objectService = $this->createMock(ObjectService::class);
        $this->config->method('hasKey')->willReturn(false);
        $this->listingMapper->method('findAll')
            ->willReturn([new Listing()]);

        // $response = $this->controller->index($objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['results' => [['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1']]], $response->getData());
    }

    public function testIndexWithMongoStorageEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService->method('findObjects')
            ->willReturn(['documents' => [['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1']]]);

        // $response = $this->controller->index($objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['results' => [['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1']]], $response->getData());
    }

    public function testShowWithMongoStorageDisabled()
    {
        $objectService = $this->createMock(ObjectService::class);
        $directoryService = $this->createMock(DirectoryService::class);

        $this->config->method('hasKey')->willReturn(false);
        $this->listingMapper->method('find')
            ->willReturn(new Listing());

        $response = $this->controller->show('1', $objectService, $directoryService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1'], $response->getData());
    }

    public function testShowWithMongoStorageEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);
        $directoryService = $this->createMock(DirectoryService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService->method('findObject')
            ->willReturn(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1']);

        $response = $this->controller->show('1', $objectService, $directoryService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1'], $response->getData());
    }

    public function testCreateWithMongoStorageDisabled()
    {
        $objectService = $this->createMock(ObjectService::class);
        $directoryService = $this->createMock(DirectoryService::class);

        $this->config->method('hasKey')->willReturn(false);
        $this->request->method('getParams')->willReturn(['title' => 'Directory test 1']);

        $this->listingMapper->method('createFromArray')
            ->willReturn(new Listing());

        $response = $this->controller->create($objectService, $directoryService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1'], $response->getData());
    }

    public function testCreateWithMongoStorageEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);
        $directoryService = $this->createMock(DirectoryService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $this->request->method('getParams')->willReturn(['title' => 'Directory test 1']);

        $objectService->method('saveObject')
            ->willReturn(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1']);

        $response = $this->controller->create($objectService, $directoryService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Directory test 1'], $response->getData());
    }

    public function testUpdateWithMongoStorageDisabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(false);
        $this->request->method('getParams')->willReturn(['title' => 'Updated title']);

        $this->listingMapper->method('updateFromArray')
            ->willReturn(new Listing());

        $response = $this->controller->update('1', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Updated title'], $response->getData());
    }

    public function testUpdateWithMongoStorageEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $this->request->method('getParams')->willReturn(['title' => 'Updated title']);

        $objectService->method('updateObject')
            ->willReturn(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Updated title']);

        $response = $this->controller->update('1', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals(['id' => '64996753-5109-4396-9f07-17040d7fb137', 'title' => 'Updated title'], $response->getData());
    }

    public function testDestroyWithMongoStorageDisabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(false);
        $this->listingMapper->method('find')
            ->willReturn(new Listing());

        $response = $this->controller->destroy('1', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithMongoStorageEnabled()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService->method('deleteObject')
            ->willReturn([]);

        $response = $this->controller->destroy('1', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithError()
    {
        $objectService = $this->createMock(ObjectService::class);

        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', '', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', '', 'someKey'],
            ['opencatalogi', 'mongodbCluster', '', 'someCluster']
        ]);

        $objectService->method('deleteObject')
            ->willThrowException(new \Exception('Delete failed'));

        $response = $this->controller->destroy('invalidId', $objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);

        $data = $response->getData();
        $this->assertIsArray($data);
        // $this->assertArrayHasKey('error', $data);
        // $this->assertEquals('Delete failed', $data['error']);
    }
}