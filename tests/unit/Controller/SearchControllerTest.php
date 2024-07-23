<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\SearchController;
use OCA\OpenCatalogi\Service\SearchService;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use Test\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SearchControllerTest extends TestCase
{
    private $appName = 'opencatalogi';
    private $request;
    private $config;
    private $searchService;
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->searchService = $this->createMock(SearchService::class);

        $this->controller = new SearchController(
            $this->appName,
            $this->request,
            $this->config
        );
    }

    public function testPage()
    {
        $response = $this->controller->page(null);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testPageWithError()
    {
        $this->controller = $this->getMockBuilder(SearchController::class)
            ->setConstructorArgs([$this->appName, $this->request, $this->config])
            ->onlyMethods(['page'])
            ->getMock();

        $this->controller->method('page')
            ->will($this->throwException(new \Exception('Template load error')));

        try {
            $this->controller->page(null);
            $this->fail('Expected exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('Template load error', $e->getMessage());
        }
    }

    public function testIndex()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->request->method('getParams')->willReturn(['_route' => 'test', 'param1' => 'value1']);

        $this->searchService->method('search')->willReturn(['results' => SearchController::TEST_ARRAY]);

        $response = $this->controller->index($this->searchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertSame(['results' => SearchController::TEST_ARRAY], $response->getData());
    }

    public function testIndexWithError()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->request->method('getParams')->willReturn(['_route' => 'test', 'param1' => 'value1']);

        $this->searchService->method('search')->willThrowException(new \Exception('Search error'));

        try {
            $this->controller->index($this->searchService);
            $this->fail('Expected exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('Search error', $e->getMessage());
        }
    }

    public function testIndexWithNoResults()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $this->request->method('getParams')->willReturn(['_route' => 'test', 'param1' => 'value1']);

        $this->searchService->method('search')->willReturn(['results' => []]);

        $response = $this->controller->index($this->searchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertSame(['results' => []], $response->getData());
    }

    public function testShow()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $id = 'd9e1467e-fc55-44c8-bf5c-bf139ac10eda';

        $this->searchService->method('search')->willReturn(['results' => [SearchController::TEST_ARRAY[$id]]]);

        $response = $this->controller->show($id, $this->searchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertSame(SearchController::TEST_ARRAY[$id], $response->getData());
    }

    public function testShowWithNonExistentId()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $id = 'non-existent-id';

        $this->searchService->method('search')->willReturn(['results' => []]);

        $response = $this->controller->show($id, $this->searchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertSame(['error' => ['code' => 404, 'message' => 'the requested resource could not be found']], $response->getData());
        $this->assertEquals(404, $response->getStatus());
    }

    public function testShowWithError()
    {
        $this->config->method('getValueString')->willReturn('someValue');
        $id = 'd9e1467e-fc55-44c8-bf5c-bf139ac10eda';

        $this->searchService->method('search')->willThrowException(new \Exception('Search error'));

        try {
            $this->controller->show($id, $this->searchService);
            $this->fail('Expected exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('Search error', $e->getMessage());
        }
    }
}
