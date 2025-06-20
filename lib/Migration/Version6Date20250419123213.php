<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OpenCatalogi\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Class Version6Date20250419123213
 * 
 * Migration to:
 * 1. Add uri columns to all tables
 * 2. Remove old tables that are no longer used
 * 3. Install and enable OpenRegister
 */
class Version6Date20250419123213 extends SimpleMigrationStep {
	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		// Remove old tables that are no longer used.
		$tablesToRemove = [
			'ocat_attachments',
			'ocat_catalogi',
			'ocat_listings',
			'ocat_organizations',
			'ocat_pages',
			'ocat_publication_types',
			'ocat_publications',
			'ocat_themes'
		];

		foreach ($tablesToRemove as $tableName) {
			if ($schema->hasTable($tableName)) {
				$schema->dropTable($tableName);
			}
		}

		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
		
	}
}
