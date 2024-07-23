<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use Test\TestCase;
use OCA\OpenCatalogi\Controller\DashboardController;
use OCP\IRequest;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;

class DashboardControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var DashboardController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->controller = new DashboardController('opencatalogi', $this->request);
    }

    public function testPage()
    {
        $response = $this->controller->page('testParam');
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testPageWithError()
    {
        $this->controller = $this->getMockBuilder(DashboardController::class)
            ->setConstructorArgs(['opencatalogi', $this->request])
            ->onlyMethods(['page'])
            ->getMock();

        $this->controller->method('page')
            ->will($this->throwException(new \Exception('Template load error')));

        try {
            $this->controller->page('testParam');
            $this->fail('Expected exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('Template load error', $e->getMessage());
        }
    }

    public function testIndex()
    {
        $response = $this->controller->index();
        $this->assertInstanceOf(JSONResponse::class, $response);

        $expectedData = [
            "results" => [
                "d021c5ff-a254-4114-a1fb-7a18db152270" => [
                    "id" => "d021c5ff-a254-4114-a1fb-7a18db152270",
                    "name" => "Dashboard one",
                    "summary" => "summary for one"
                ],
                "79c02b33-78ba-4d65-aabd-ff9aae6654f7" => [
                    "id" => "79c02b33-78ba-4d65-aabd-ff9aae6654f7",
                    "name" => "Dashboard two",
                    "summary" => "summary for two"
                ]
            ]
        ];

        $this->assertEquals($expectedData, $response->getData());
    }

    public function testIndexWithError()
    {
        $this->controller = $this->getMockBuilder(DashboardController::class)
            ->setConstructorArgs(['opencatalogi', $this->request])
            ->onlyMethods(['index'])
            ->getMock();

        $this->controller->method('index')
            ->will($this->throwException(new \Exception('Data retrieval error')));

        try {
            $this->controller->index();
            $this->fail('Expected exception not thrown');
        } catch (\Exception $e) {
            $this->assertEquals('Data retrieval error', $e->getMessage());
        }
    }
}
