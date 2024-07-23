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
class Version6Date20240723125106 extends SimpleMigrationStep {

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

		if($schema->hasTable(tableName: 'publications') === false) {
			$table = $schema->createTable(tableName: 'publications');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'reference', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
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
				'length' => 255,
				'notnull' => false,
			]);
			$table->addColumn(name: 'category', typeName: TYPES::STRING, options: [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn(name: 'portal', typeName: TYPES::STRING);
			$table->addColumn(name: 'catalogi', typeName: TYPES::STRING);
			$table->addColumn(name: 'meta_data', typeName: TYPES::STRING);
			$table->addColumn(name: 'publication_date', typeName: TYPES::DATETIME);
			$table->addColumn(name: 'modified', typeName: TYPES::DATETIME);
			$table->addColumn(name: 'featured', typeName: TYPES::BOOLEAN, options: [
				'notnull' => false,
			]
			);
			$table->addColumn(name: 'organization', typeName: TYPES::JSON, options: [
				'default' => [],
				'notnull' => false,
			]);
			$table->addColumn(name: 'data', typeName: TYPES::JSON, options: [
				'default' => [],
				'notnull' => false,
			]);
			$table->addColumn(name: 'attachments', typeName: TYPES::JSON, options: [
				'default' => [],
				'notnull' => false,
			]);
			$table->addColumn(name: 'attachment_count', typeName: TYPES::INTEGER);
			$table->addColumn(name: 'schema', typeName: TYPES::STRING);
			$table->addColumn(name: 'status', typeName: TYPES::STRING);
			$table->addColumn(name: 'license', typeName: TYPES::STRING);
			$table->addColumn(name: 'themes', typeName: TYPES::JSON, options: [
				'default' => [],
				'notnull' => false,
			]);
			$table->addColumn(name: 'anonymization', typeName: TYPES::JSON, options: [
				'default' => [],
				'notnull' => false,
			]);
			$table->addColumn(name: 'language_object', typeName: TYPES::JSON, options: [
				'default' => [],
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
