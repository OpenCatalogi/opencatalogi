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
            'organisationName' => 'my-organisation',
            'organisationOin' => '',
            'organisationPki' => ''
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

        // Zorg ervoor dat de mock de juiste waarden retourneert
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
}
