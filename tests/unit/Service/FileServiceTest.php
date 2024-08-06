<?php

namespace OCA\OpenCatalogi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use OCA\OpenCatalogi\Service\FileService;
use OCP\IAppConfig;
use OCP\IUser;
use OCP\IUserSession;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FileServiceTest extends TestCase
{
    /** @var MockObject|IUserSession */
    private $userSession;

    /** @var MockObject|LoggerInterface */
    private $logger;

    /** @var MockObject|IAppConfig */
    private $config;

    /** @var MockObject|Client */
    private $client;

    /** @var FileService */
    private $fileService;

    protected function setUp(): void
    {
        $this->userSession = $this->createMock(IUserSession::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->config = $this->createMock(IAppConfig::class);
        $this->client = $this->createMock(Client::class);

        $this->fileService = new FileService($this->userSession, $this->logger, $this->config);

        // Inject the mock client into the file service
        $reflection = new \ReflectionClass($this->fileService);
        $clientProperty = $reflection->getProperty('client');
        $clientProperty->setAccessible(true);
        $clientProperty->setValue($this->fileService, $this->client);
    }

    public function testSetAppName()
    {
        $this->fileService->setAppName('newAppName');
        $reflection = new \ReflectionClass($this->fileService);
        $appNameProperty = $reflection->getProperty('appName');
        $appNameProperty->setAccessible(true);
        $this->assertEquals('newAppName', $appNameProperty->getValue($this->fileService));
    }

    
}
