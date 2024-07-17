<?php

use PHPUnit\Framework\TestCase;
use OCA\OpenCatalogi\Service\ObjectService;

class ObjectServiceTest extends TestCase
{
    private $objectService;

    protected function setUp(): void
    {
        $this->objectService = $this->getMockBuilder(ObjectService::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();
    }

    public function testSaveObject()
    {
        // Mock the ObjectService methods
        $this->objectService->method('getClient')
                            ->willReturn($this->createMock(Client::class));

        // Call your saveObject method and make assertions
        // ...
    }

    public function testFindObjects()
    {
        // Mock the ObjectService methods
        $this->objectService->method('findObject')
                            ->willReturn(['id' => '1', 'name' => 'Document1']);

        // Call your findObjects method and make assertions
        // ...
    }
    
    // Voeg andere tests toe met dezelfde aanpak
}
