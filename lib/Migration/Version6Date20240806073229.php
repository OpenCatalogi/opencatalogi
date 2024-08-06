<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OpenCatalogi\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * FIXME Auto-generated migration step: Please modify to your needs!
 */
class Version6Date20240806073229 extends SimpleMigrationStep {

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

		/**
		 * Let build the themas tabsle
		 */
		if($schema->hasTable(tableName: 'themas') === false) {
			$table = $schema->createTable(tableName: 'themas');

			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'summary', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'length' => 255,
				'notnull' => false,
			]);
			$table->addColumn(name: 'image', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);

			
			$table->setPrimaryKey(columnNames: ['id']);
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
