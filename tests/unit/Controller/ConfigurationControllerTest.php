<?php

namespace OCA\OpenCatalogi\Tests\Controller;

use Test\TestCase;
use OCA\OpenCatalogi\Controller\ConfigurationController;
use OCP\IRequest;
use OCP\IAppConfig;
use OCP\AppFramework\Http\JSONResponse;
use PHPUnit\Framework\MockObject\MockObject;

class ConfigurationControllerTest extends TestCase
{
    /** @var MockObject|IRequest */
    private $request;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var ConfigurationController */
    private $controller;

    protected function setUp(): void
    {
        $this->request = $this->createMock(IRequest::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->controller = new ConfigurationController('opencatalogi', $this->config, $this->request);
    }

    public function testIndex()
    {
        $defaults = [
            'drcLocation' => '',
            'drcKey' => '',
            'orcLocation' => '',
            'orcKey' => '',
            'mongodbLocation' => '',
            'mongodbKey' => '',
            'mongodbCluster' => '',
            'elasticLocation' => '',
            'elasticKey' => '',
            'elasticIndex' => '',
            'organizationName' => 'my-organization',
            'organizationOin' => '',
            'organizationPki' => ''
        ];

        $this->config->method('getValueString')
            ->will($this->returnCallback(function ($appName, $key, $default) use ($defaults) {
                return $defaults[$key] === '' ? 'someValue' : $defaults[$key];
            }));

        $response = $this->controller->index();
        $this->assertInstanceOf(JSONResponse::class, $response);

        $expectedData = array_map(function ($value) {
            return $value === '' ? 'someValue' : $value;
        }, $defaults);

        $this->assertEquals($expectedData, $response->getData());
    }

    public function testIndexWithError()
    {
        $this->config->method('getValueString')
            ->will($this->throwException(new \Exception('Error retrieving configuration')));

        $response = $this->controller->index();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertArrayHasKey('error', $response->getData());
        $this->assertEquals('Error retrieving configuration', $response->getData()['error']);
    }

    public function testCreate()
    {
        $this->request->method('getParams')->willReturn([
            'drcLocation' => 'newLocation',
            'drcKey' => 'newKey'
        ]);

        $this->config->expects($this->exactly(2))
            ->method('setValueString')
            ->withConsecutive(
                ['opencatalogi', 'drcLocation', 'newLocation'],
                ['opencatalogi', 'drcKey', 'newKey']
            );

        $this->config->method('getValueString')
            ->will($this->returnCallback(function ($appName, $key, $default) {
                $values = [
                    'drcLocation' => 'newLocation',
                    'drcKey' => 'newKey'
                ];
                return $values[$key] ?? 'someValue';
            }));

        $response = $this->controller->create();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertEquals([
            'drcLocation' => 'newLocation',
            'drcKey' => 'newKey'
        ], $response->getData());
    }

    public function testCreateWithError()
    {
        $this->request->method('getParams')->willReturn([
            'drcLocation' => 'newLocation',
            'drcKey' => 'newKey'
        ]);

        $this->config->method('setValueString')
            ->will($this->throwException(new \Exception('Error saving configuration')));

        $response = $this->controller->create();
        $this->assertInstanceOf(JSONResponse::class, $response);
        $this->assertArrayHasKey('error', $response->getData());
        $this->assertEquals('Error saving configuration', $response->getData()['error']);
    }
}
