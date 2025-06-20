<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use Test\TestCase;
use OCA\OpenCatalogi\Controller\PublicationTypesController;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;

class PublicationTypesControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|ObjectService */
    private $objectService;

    /** @var PublicationTypesController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->controller = new PublicationTypesController('opencatalogi', $this->request, $this->config);

        $this->config->method('getValueString')
            ->will($this->returnValue('someValue'));
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndex()
    {
        $this->objectService->method('findObjects')
            ->willReturn(['documents' => PublicationTypesController::TEST_ARRAY]);

        $response = $this->controller->index($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(["results" => PublicationTypesController::TEST_ARRAY], $response->getData());
    }

    public function testIndexWithInvalidFilters()
    {
        $this->request->method('getParams')->willReturn(['_invalid' => 'value']);

        $this->objectService->method('findObjects')
            ->willReturn(['documents' => []]);

        $response = $this->controller->index($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(["results" => []], $response->getData());
    }

    public function testShow()
    {
        $id = '6892aeb1-d92d-4da5-ad41-f1c3278f40c2';
        $this->objectService->method('findObject')
            ->willReturn(PublicationTypesController::TEST_ARRAY[$id]);

        $response = $this->controller->show($id, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(PublicationTypesController::TEST_ARRAY[$id], $response->getData());
    }

    public function testShowWithNonExistentId()
    {
        $id = 'non-existent-id';
        $this->objectService->method('findObject')
            ->willReturn([]);

        $response = $this->controller->show($id, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testCreate()
    {
        $data = [
            "id" => "new-id",
            "title" => "New Publication Type",
            "description" => "A new testing Publication Type",
            "version" => "0.0.1",
            "properties" => '{}',
            "_schema" => "Publication Type"
        ];

        $this->request->method('getParams')->willReturn($data);
        $this->objectService->method('saveObject')
            ->willReturn($data);

        $response = $this->controller->create($this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($data, $response->getData());
    }

    public function testUpdate()
    {
        $id = '6892aeb1-d92d-4da5-ad41-f1c3278f40c2';
        $data = [
            "title" => "Updated Publication Type",
            "description" => "An updated testing metadata",
            "version" => "0.0.2",
            "properties" => '{}'
        ];

        $this->request->method('getParams')->willReturn($data);

        $updatedData = array_merge(PublicationTypesController::TEST_ARRAY[$id], $data);
        $this->objectService->method('updateObject')
            ->willReturn($updatedData);

        $response = $this->controller->update($id, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals($updatedData, $response->getData());
    }

    public function testUpdateWithNonExistentId()
    {
        $id = 'non-existent-id';
        $data = [
            "title" => "Updated Publication Type",
            "description" => "An updated testing metadata",
            "version" => "0.0.2",
            "properties" => '{}'
        ];

        $this->request->method('getParams')->willReturn($data);

        $this->objectService->method('updateObject')
            ->willReturn([]);

        $response = $this->controller->update($id, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroy()
    {
        $id = '6892aeb1-d92d-4da5-ad41-f1c3278f40c2';
        $this->objectService->method('deleteObject')
            ->willReturn([]);

        $response = $this->controller->destroy($id, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    public function testDestroyWithNonExistentId()
    {
        $id = 'non-existent-id';
        $this->objectService->method('deleteObject')
            ->willReturn([]);

        $response = $this->controller->destroy($id, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([], $response->getData());
    }

    // Exception Handling Tests
    public function testIndexThrowsException()
    {
        $this->objectService->method('findObjects')
            ->willThrowException(new \Exception('Database error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Database error');

        $this->controller->index($this->objectService);
    }

    public function testShowThrowsException()
    {
        $id = '6892aeb1-d92d-4da5-ad41-f1c3278f40c2';
        $this->objectService->method('findObject')
            ->willThrowException(new \Exception('Object not found'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Object not found');

        $this->controller->show($id, $this->objectService);
    }

    public function testCreateThrowsException()
    {
        $data = [
            "id" => "new-id",
            "title" => "New Publication Type",
            "description" => "A new testing metadata",
            "version" => "0.0.1",
            "properties" => '{}',
            "_schema" => "metadata"
        ];

        $this->request->method('getParams')->willReturn($data);
        $this->objectService->method('saveObject')
            ->willThrowException(new \Exception('Save failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Save failed');

        $this->controller->create($this->objectService);
    }

    public function testUpdateThrowsException()
    {
        $id = '6892aeb1-d92d-4da5-ad41-f1c3278f40c2';
        $data = [
            "title" => "Updated Publication Type",
            "description" => "An updated testing metadata",
            "version" => "0.0.2",
            "properties" => '{}'
        ];

        $this->request->method('getParams')->willReturn($data);

        $this->objectService->method('updateObject')
            ->willThrowException(new \Exception('Update failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Update failed');

        $this->controller->update($id, $this->objectService);
    }

    public function testDestroyThrowsException()
    {
        $id = '6892aeb1-d92d-4da5-ad41-f1c3278f40c2';

        $this->objectService->method('deleteObject')
            ->willThrowException(new \Exception('Delete failed'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Delete failed');

        $this->controller->destroy($id, $this->objectService);
    }
}
