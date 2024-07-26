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
class Version6Date20240725114845 extends SimpleMigrationStep {

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

		if($schema->hasTable(tableName: 'metadata') === false) {
			$table = $schema->createTable(tableName: 'metadata');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'version', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'length' => 255,
				'notnull' => false,
			]);
			$table->addColumn(name: 'required', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'properties', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'listings') === false) {
			$table = $schema->createTable(tableName: 'listings');
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
			$table->addColumn(name: 'search', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'directory', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'metadata', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'status', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'last_sync', typeName: TYPES::DATETIME, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'default', typeName: TYPES::BOOLEAN, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'available', typeName: TYPES::BOOLEAN, options: [
				'notnull' => false,
			]);

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'organizations') === false) {
			$table = $schema->createTable(tableName: 'organizations');
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
			$table->addColumn(name: 'oin', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'tooi', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'rsin', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'pki', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'attachments') === false) {
			$table = $schema->createTable(tableName: 'attachments');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'reference', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
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
			$table->addColumn(name: 'labels', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'access_url', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'download_url', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'type', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'extension', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'size', typeName: TYPES::INTEGER, options: [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn(name: 'version_of', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'hash', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'anonymization', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'language', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'published', typeName: TYPES::DATETIME, options: [
				'notNull' => false
			]);
			$table->addColumn(name: 'modified', typeName: TYPES::DATETIME, options: [
				'notNull' => false
			]);
			$table->addColumn(name: 'license', typeName: TYPES::STRING);


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
