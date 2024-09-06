<?php


namespace OCA\OpenCatalogi\Dashboard;

use OCP\Dashboard\IWidget;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Util;

use OCA\OpenCatalogi\AppInfo\Application;

class UnpublishedAttachmentsWidget implements IWidget
{

    public function __construct(
        private IL10N $l10n,
        private IURLGenerator $url
    ) {}

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return 'opencatalogi_unpublished_attachments_widget';
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return $this->l10n->t('Concept bijlage');
    }

    /**
     * @inheritDoc
     */
    public function getOrder(): int
    {
        return 10;
    }

    /**
     * @inheritDoc
     */
    public function getIconClass(): string
    {
        return 'icon-catalogi-widget';
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function load(): void
    {
        Util::addScript(Application::APP_ID, Application::APP_ID . '-unpublishedAttachmentsWidget');
        Util::addStyle(Application::APP_ID, 'dashboardWidgets');
    }
}
