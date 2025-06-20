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
 * Class Version6Date20241208222530
 * 
 * Migration to add uri columns to all tables and create missing tables
 */
class Version6Date20241208222530 extends SimpleMigrationStep {

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
		/**
		 * @var ISchemaWrapper $schema
		 */
		$schema = $schemaClosure();

		// Update the ocat_attachments table
		$table = $schema->getTable('ocat_attachments');
		if ($table->hasColumn('uri') === false) {
			$table->addColumn(
				name: 'uri',
				typeName: Types::STRING,
				options: [
					'notnull' => true,
					'length'  => 255
				]
			)->setDefault('');
		}

		// Update catalogi table
		$table = $schema->getTable('ocat_catalogi');
		if ($table->hasColumn('uri') === false) {
			$table->addColumn(
				name: 'uri',
				typeName: Types::STRING,
				options: [
					'notnull' => true,
					'length'  => 255
				]
			)->setDefault('');
		}

		// Update organizations table
		$table = $schema->getTable('ocat_organizations');
		if ($table->hasColumn('uri') === false) {
			$table->addColumn(
				name: 'uri',
				typeName: Types::STRING,
				options: [
					'notnull' => true,
					'length'  => 255
				]
			)->setDefault('');
		}

		// Update publications table
		$table = $schema->getTable('ocat_publications');
		if ($table->hasColumn('uri') === false) {
			$table->addColumn(
				name: 'uri',
				typeName: Types::STRING,
				options: [
					'notnull' => true,
					'length'  => 255
				]
			)->setDefault('');
		}

		// Update publication types table
		$table = $schema->getTable('ocat_publication_types');
		if ($table->hasColumn('uri') === false) {
			$table->addColumn(
				name: 'uri',
				typeName: Types::STRING,
				options: [
					'notnull' => true,
					'length'  => 255
				]
			)->setDefault('');
			if (!$table->hasIndex('ocat_publication_uuid_index')) {
				$table->addIndex(['uuid'], 'ocat_publication_uuid_index');
			}
		}

		// Update themes table
		$table = $schema->getTable('ocat_themes');
		if ($table->hasColumn('uri') === false) {
			$table->addColumn(
				name: 'uri',
				typeName: Types::STRING,
				options: [
					'notnull' => true,
					'length'  => 255
				]
			)->setDefault('');
			if (!$table->hasIndex('ocat_themes_uuid_index')) {
				$table->addIndex(['uuid'], 'ocat_themes_uuid_index');
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
