<?php

namespace OCA\OpenCatalogi\Cron;

use OCP\WorkflowEngine\IOperation ;

class AutomatedPublishing extends IOperation  {

	public function getDisplayName(): string	{
        return $this->l->t('Automated publishing');
    }

	public function getDescription(): string	{
        return $this->l->t('Automaticly publish publiations if they meet predefined parameters');
    }

	public function getIcon(): string	{
        return \OC::$server->getURLGenerator()->imagePath('opencatalogi','app.svg');
    }

    public function validateOperation(): bool {
        return true;
    }

    /**
     * Determens for what kind of users the opertation is available
     *
     * var $scope is presented from the IManager as a constant with 0 for ADMIN and 1 for USER
     */
	public function isAvailableForScope(int $scope): bool	{
        if (scope === 0){
            return false;
        }
        return false;
    }


}
