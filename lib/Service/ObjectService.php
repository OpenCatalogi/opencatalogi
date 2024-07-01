<?php

namespace OCA\OpenCatalog\Service;

use GuzzleHttp\Client;
use Symfony\Component\Uid\Uuid;

class ObjectService
{

	public const BASE_OBJECT = [
		'database'   => 'objects',
		'collection' => 'json',
	];

	private function getClient(array $config): Client
	{
		$guzzleConf = $config;
		unset($guzzleConf['mongodbCluster']);

		return new Client($config);
	}
	public function saveObject(array $data, array $config): array
	{
		$client = $this->getClient(config: $config);

		$object 			      = self::BASE_OBJECT;
		$object['dataSource']     = $config['mongodbCluster'];
		$object['document']       = $data;
		$object['document']['_id'] = Uuid::v4();

		$result = $client->post(
			uri: 'action/insertOne',
			options: ['json' => $object],
		);
		$resultData =  json_decode(
			json: $result->getBody()->getContents(),
			associative: true
		);
		$id = $resultData['insertedId'];

		return $this->findObject(filters: ['_id' => $id], config: $config);
	}

	public function findObjects(array $filters, array $config): array
	{
		$client = $this->getClient(config: $config);

		$object               = self::BASE_OBJECT;
		$object['dataSource'] = $config['mongodbCluster'];
		$object['filter']     = $filters;

		$returnData = $client->post(
			uri: 'action/find',
			options: ['json' => $object]
		);

		return json_decode(
			json: $returnData->getBody()->getContents(),
			associative: true
		);
	}

	public function findObject(array $filters, array $config): array
	{
		$client = $this->getClient(config: $config);

		$object               = self::BASE_OBJECT;
		$object['filter']     = $filters;
		$object['dataSource'] = $config['mongodbCluster'];

		$returnData = $client->post(
			uri: 'action/findOne',
			options: ['json' => $object]
		);

		return json_decode(
			json: $returnData->getBody()->getContents(),
			associative: true
		)['document'];
	}


	public function updateObject(array $filters, array $update, array $config): array
	{
		$client = $this->getClient(config: $config);

		$object                   = self::BASE_OBJECT;
		$object['filter']         = $filters;
		$object['update']['$set'] = $update;
		$object['dataSource']     = $config['mongodbCluster'];

		$returnData = $client->post(
			uri: 'action/updateOne',
			options: ['json' => $object]
		);

		return $this->findObject($filters, $config);
	}

	public function deleteObject(array $filters, array $config): array
	{
		$client = $this->getClient(config: $config);

		$object                   = self::BASE_OBJECT;
		$object['filter']         = $filters;
		$object['dataSource']     = $config['mongodbCluster'];

		$returnData = $client->post(
			uri: 'action/deleteOne',
			options: ['json' => $object]
		);

		return [];
	}

	public function aggregateObjects(array $filters, array $pipeline, array $config):array
	{
		$client = $this->getClient(config: $config);

		$object               = self::BASE_OBJECT;
		$object['filter']     = $filters;
		$object['pipeline']   = $pipeline;
		$object['dataSource'] = $config['mongodbCluster'];

		$returnData = $client->post(
			uri: 'action/aggregate',
			options: ['json' => $object]
		);

		return json_decode(
			json: $returnData->getBody()->getContents(),
			associative: true
		);

	}

}
