<?php

namespace OCA\OpenCatalogi\Tests\AppInfo;

use OCA\OpenCatalogi\AppInfo\Application;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ApplicationTest extends TestCase
{
    /** @var Application */
    private $application;

    protected function setUp(): void
    {
        $this->application = new Application();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Application::class, $this->application);
        $this->assertEquals('opencatalogi', Application::APP_ID);
    }

    public function testRegister()
    {
        $context = $this->createMock(IRegistrationContext::class);
        
        // Capture the output of the include statement
        ob_start();
        $this->application->register($context);
        $output = ob_get_clean();

        // Check that the autoload file was included (if it exists)
        $this->assertFileExists(__DIR__ . '/../../../vendor/autoload.php');
    }

    public function testBoot()
    {
        $context = $this->createMock(IBootContext::class);
        $this->application->boot($context);

        // Ensure no exceptions are thrown and the method is callable
        $this->assertTrue(method_exists($this->application, 'boot'));
    }
}
