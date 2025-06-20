<?php

namespace OCA\OpenCatalogi\Cron;

use OCA\OpenCatalogi\Service\DirectoryService;
use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\BackgroundJob\IJob;

/**
 *
 * Docs: https://docs.nextcloud.com/server/latest/developer_manual/basics/backgroundjobs.html
 */
class DirectorySync extends TimedJob {

    public function __construct(
        ITimeFactory $time,
        private readonly DirectoryService $directoryService
    ) {
        parent::__construct($time);
        
		// Run every minute @todochange to hour
		$this->setInterval(60);

		// Delay until low-load time
		$this->setTimeSensitivity(IJob::TIME_INSENSITIVE);

        // Only run one instance of this job at a time.
        $this->setAllowParallelRuns(false);
    }//end __construct

    /**
     * Lets run the cron sync
     * 
     * @param array $arguments
     * @param DirectoryService $directoryService
     */
    protected function run($arguments) {
        // @todo disabled for now, triggers to many times and current state is broken and needs fixing/refactor
        // $this->directoryService->doCronSync();
    }


}
