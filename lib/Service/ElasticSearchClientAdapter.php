<?php

namespace OCA\OpenCatalogi\Service;

use Elastic\Elasticsearch\Client;

class ElasticSearchClientAdapter
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search(array $params)
    {
        return $this->client->search($params);
    }

    public function index(array $params)
    {
        return $this->client->index($params);
    }

    public function get(array $params)
    {
        return $this->client->get($params);
    }

    public function update(array $params)
    {
        return $this->client->update($params);
    }

    public function delete(array $params)
    {
        return $this->client->delete($params);
    }
}