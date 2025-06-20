<?php
/**
 * Adapter for Elasticsearch client operations.
 *
 * Provides a wrapper around the Elasticsearch client to facilitate search, indexing,
 * retrieval, updating, and deletion of documents.
 *
 * @category Service
 * @package  OCA\OpenCatalogi\Service
 *
 * @author    Conduction Development Team <info@conduction.nl>
 * @copyright 2024 Conduction B.V.
 * @license   EUPL-1.2 https://joinup.ec.europa.eu/collection/eupl/eupl-text-eupl-12
 *
 * @version GIT: <git_id>
 *
 * @link https://www.OpenCatalogi.nl
 */

namespace OCA\OpenCatalogi\Service;

use Elastic\Elasticsearch\Client;

/**
 * Adapter for Elasticsearch client operations.
 *
 * Provides a wrapper around the Elasticsearch client to facilitate search, indexing,
 * retrieval, updating, and deletion of documents.
 */
class ElasticSearchClientAdapter
{

    /**
     * @var Client The Elasticsearch client instance
     */
    private $client;


    /**
     * Constructor for ElasticSearchClientAdapter
     *
     * @param Client $client The Elasticsearch client instance
     */
    public function __construct(Client $client)
    {
        $this->client = $client;

    }//end __construct()


    /**
     * Perform a search operation
     *
     * @param  array $params The search parameters
     * @return array The search results
     */
    public function search(array $params)
    {
        // Execute the search request and return the results
        return $this->client->search($params);

    }//end search()


    /**
     * Index a document
     *
     * @param  array $params The indexing parameters
     * @return array The indexing response
     */
    public function index(array $params)
    {
        // Index the document and return the response
        return $this->client->index($params);

    }//end index()


    /**
     * Get a document by its ID
     *
     * @param  array $params The get parameters
     * @return array The document data
     */
    public function get(array $params)
    {
        // Retrieve the document and return it
        return $this->client->get($params);

    }//end get()


    /**
     * Update a document
     *
     * @param  array $params The update parameters
     * @return array The update response
     */
    public function update(array $params)
    {
        // Update the document and return the response
        return $this->client->update($params);

    }//end update()


    /**
     * Delete a document
     *
     * @param  array $params The delete parameters
     * @return array The delete response
     */
    public function delete(array $params)
    {
        // Delete the document and return the response
        return $this->client->delete($params);

    }//end delete()


}//end class
