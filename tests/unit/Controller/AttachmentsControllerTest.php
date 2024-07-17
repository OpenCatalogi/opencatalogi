<?php

use PHPUnit\Framework\TestCase;
use OCA\OpenCatalogi\Controller\AttachmentsController;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\AppFramework\Http\JSONResponse;

class AttachmentsControllerTest extends TestCase
{
    private $controller;
    private $request;
    private $config;
    private $objectService;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);

        $this->controller = new AttachmentsController(
            'opencatalogi',
            $this->request,
            $this->config
        );
    }

    public function testIndex()
    {
        // Mock the configuration values
        $this->config->method('getValueString')
            ->will($this->returnValueMap([
                ['opencatalogi', 'mongodbLocation', '', 'http://localhost:27017'],
                ['opencatalogi', 'mongodbKey', '', 'test_key'],
                ['opencatalogi', 'mongodbCluster', '', 'test_cluster'],
            ]));

        // Mock the request parameters
        $this->request->method('getParams')
            ->willReturn(['filter1' => 'value1', 'filter2' => 'value2']);

        // Mock the object service response
        $expectedResult = [
            'documents' => [
                ['id' => '1', 'name' => 'Document1'],
                ['id' => '2', 'name' => 'Document2']
            ]
        ];
        $this->objectService->method('findObjects')
            ->willReturn($expectedResult);

        // Call the controller method
        $response = $this->controller->index($this->objectService);

        // Assert the response
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => $expectedResult['documents']], $response->getData());
    }
}
