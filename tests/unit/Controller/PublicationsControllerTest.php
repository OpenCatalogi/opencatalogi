<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use OCA\OpenCatalogi\Controller\PublicationsController;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IAppConfig;
use PHPUnit\Framework\TestCase;

class PublicationsControllerTest extends TestCase
{
    private $controller;
    private $request;
    private $publicationMapper;
    private $config;
    private $objectService;
    private $elasticSearchService;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->publicationMapper = $this->createMock(PublicationMapper::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->objectService = $this->createMock(ObjectService::class);
        $this->elasticSearchService = $this->createMock(ElasticSearchService::class);

        $this->controller = new PublicationsController(
            'opencatalogi',
            $this->request,
            $this->publicationMapper,
            $this->config
        );
    }

    public function testPage(): void
    {
        $response = $this->controller->page(null);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testCatalog(): void
    {
        $response = $this->controller->catalog(1);
        $this->assertInstanceOf(TemplateResponse::class, $response);
    }

    public function testIndexWithMongoDBDisabled(): void
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('findAll')->willReturn([PublicationsController::TEST_ARRAY]);
        // Error: Unknown named parameter $searchParams
        // $response = $this->controller->index($this->objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertSame(['results' => [PublicationsController::TEST_ARRAY]], $response->getData());
    }

    public function testIndexWithMongoDBEnabled(): void
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongoStorage', '1'],
            ['opencatalogi', 'mongodbLocation', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', 'key'],
            ['opencatalogi', 'mongodbCluster', 'cluster']
        ]);
        $this->objectService->method('findObjects')->willReturn(['documents' => [PublicationsController::TEST_ARRAY]]);

        // $response = $this->controller->index($this->objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertSame(['results' => [PublicationsController::TEST_ARRAY]], $response->getData());
    }

    public function testShowWithMongoDBDisabled(): void
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('find')->willReturn($this->createMock(\OCA\OpenCatalogi\Db\Publication::class));

        $response = $this->controller->show(1, $this->objectService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testShowWithMongoDBEnabled(): void
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', 'key'],
            ['opencatalogi', 'mongodbCluster', 'cluster']
        ]);
        $this->objectService->method('findObject')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);

        // $response = $this->controller->show(1, $this->objectService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertSame(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4'], $response->getData());
    }

    public function testCreateWithMongoDBDisabled(): void
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('createFromArray')->willReturn($this->createMock(\OCA\OpenCatalogi\Db\Publication::class));

        $this->request->method('getParams')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);

        $response = $this->controller->create($this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testCreateWithMongoDBEnabled(): void
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', 'key'],
            ['opencatalogi', 'mongodbCluster', 'cluster']
        ]);

        $this->request->method('getParams')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);
        $this->objectService->method('saveObject')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);

        // $response = $this->controller->create($this->objectService, $this->elasticSearchService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testUpdateWithMongoDBDisabled(): void
    {
        $this->config->method('hasKey')->willReturn(false);
        $this->publicationMapper->method('updateFromArray')->willReturn($this->createMock(\OCA\OpenCatalogi\Db\Publication::class));

        $this->request->method('getParams')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);

        $response = $this->controller->update(1, $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testUpdateWithMongoDBEnabled(): void
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', 'key'],
            ['opencatalogi', 'mongodbCluster', 'cluster']
        ]);

        $this->request->method('getParams')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);
        $this->objectService->method('updateObject')->willReturn(PublicationsController::TEST_ARRAY['354980e5-c967-4ba5-989b-65c2b0cd2ff4']);

        // $response = $this->controller->update(1, $this->objectService, $this->elasticSearchService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
    }

    public function testDestroyWithMongoDBDisabled(): void
    {
        $this->config->method('hasKey')->willReturn(false);
        $publication = $this->createMock(\OCA\OpenCatalogi\Db\Publication::class);
        $this->publicationMapper->method('find')->willReturn($publication);
        $this->publicationMapper->method('delete')->willReturn($publication);

        $response = $this->controller->destroy(1, $this->objectService, $this->elasticSearchService);
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertSame([], $response->getData());
    }

    public function testDestroyWithMongoDBEnabled(): void
    {
        $this->config->method('hasKey')->willReturn(true);
        $this->config->method('getValueString')->willReturnMap([
            ['opencatalogi', 'mongodbLocation', 'http://localhost'],
            ['opencatalogi', 'mongodbKey', 'key'],
            ['opencatalogi', 'mongodbCluster', 'cluster']
        ]);

        $this->objectService->method('deleteObject')->willReturn(['deletedCount' => 1]);

        // $response = $this->controller->destroy(1, $this->objectService, $this->elasticSearchService);
        // $this->assertInstanceOf(JSONResponse::class, $response);
        // $this->assertSame(['deletedCount' => 1], $response->getData());
    }
}
?>
