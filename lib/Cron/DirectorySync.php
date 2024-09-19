<?php

namespace OCA\OpenCatalogi\Cron;

use OCA\OpenCatalogi\Service\DirectoryService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;

/**
 *
 * Docs: https://docs.nextcloud.com/server/latest/developer_manual/basics/backgroundjobs.html
 */
class DirectorySync extends TimedJob {

    private DirectoryService $directoryService;

    public function __construct(ITimeFactory $time, DirectoryService $directoryService) {
        parent::__construct($time);
        $this->directoryService = $directoryService;

        // Run once an hour
        $this->setInterval(3600);

        // Only run one instance of this job at a time
        $this->setAllowParallelRuns(false);
    }

    protected function run($arguments) {
        $this->directoryService->doCronSync();
    }


}
