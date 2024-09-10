<?php

declare(strict_types=1);

namespace OCA\OpenCatalogi\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCA\OpenCatalogi\Dashboard\CatalogWidget;
use OCA\OpenCatalogi\Dashboard\UnpublishedPublicationsWidget;
use OCA\OpenCatalogi\Dashboard\UnpublishedAttachmentsWidget;

class Application extends App implements IBootstrap {
	public const APP_ID = 'opencatalogi';

	/** @psalm-suppress PossiblyUnusedMethod */
	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
		include_once __DIR__ . '/../../vendor/autoload.php';
		$context->registerDashboardWidget(CatalogWidget::class);
		$context->registerDashboardWidget(UnpublishedPublicationsWidget::class);
		$context->registerDashboardWidget(UnpublishedAttachmentsWidget::class);
	}

	public function boot(IBootContext $context): void {
	}
}
