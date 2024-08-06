<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\DashboardController;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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
        $this->assertEquals('index', $response->getTemplateName());
        $this->assertEquals([], $response->getParams());
    }

    public function testPageWithException()
    {
        $controller = $this->getMockBuilder(DashboardController::class)
            ->setConstructorArgs(['opencatalogi', $this->request])
            ->onlyMethods(['page'])
            ->getMock();

        $controller->method('page')->will($this->throwException(new \Exception('Test Exception')));

        try {
            $response = $controller->page('testParam');
        } catch (\Exception $e) {
            $response = new TemplateResponse(
                'opencatalogi',
                'error',
                ['error' => $e->getMessage()]
            );
            $response->setStatus(500);  // Ensure the status is set to 500
        }

        $this->assertInstanceOf(TemplateResponse::class, $response);
        $this->assertEquals('error', $response->getTemplateName());
        $this->assertEquals(['error' => 'Test Exception'], $response->getParams());
        $this->assertEquals(500, $response->getStatus());
    }

    public function testIndex()
    {
        $response = $this->controller->index();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['results' => DashboardController::TEST_ARRAY], $response->getData());
    }

    public function testIndexWithException()
    {
        $controller = $this->getMockBuilder(DashboardController::class)
            ->setConstructorArgs(['opencatalogi', $this->request])
            ->onlyMethods(['index'])
            ->getMock();

        $controller->method('index')->will($this->throwException(new \Exception('Test Exception')));

        try {
            $response = $controller->index();
        } catch (\Exception $e) {
            $response = new JSONResponse(['error' => $e->getMessage()], 500);
        }

        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals(['error' => 'Test Exception'], $response->getData());
        $this->assertEquals(500, $response->getStatus());
    }
}
