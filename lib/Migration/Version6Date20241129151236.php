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
 * FIXME Auto-generated migration step: Please modify to your needs!
 */
class Version6Date20241129151236 extends SimpleMigrationStep {

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

		if ($schema->hasTable(tableName: 'ocat_pages') === false) {
			$table = $schema->createTable(tableName: 'ocat_pages');		
		
			// Primary key and identifier columns
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: ['autoincrement' => true, 'notnull' => true, 'length' => 4]);
			$table->addColumn(name: 'uuid', typeName: Types::STRING, options: ['notnull' => true, 'length' => 255]);
			$table->addColumn(name: 'version', typeName: Types::STRING, options: ['notnull' => true, 'length' => 255, 'default' => '0.0.1']);
						
			// Meta columns
			$table->addColumn(name: 'name', typeName: Types::STRING, options: ['notnull' => true, 'length' => 255]);
			$table->addColumn(name: 'slug', typeName: Types::STRING, options: ['notnull' => true, 'length' => 255]);
			$table->addColumn(name: 'contents', typeName: Types::JSON, options: ['notnull' => false]);
			$table->addColumn(name: 'updated', typeName: Types::DATETIME, options: ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);
			$table->addColumn(name: 'created', typeName: Types::DATETIME, options: ['notnull' => true, 'default' => 'CURRENT_TIMESTAMP']);

			// Keys and indexes
			$table->setPrimaryKey(columnNames: ['id']);
			$table->addIndex(['uuid'], 'ocat_pages_uuid_index');
			$table->addIndex(['slug'], 'ocat_pages_slug_index');
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
