<?php
/**
 * Service for managing search operations and related functionalities.
 *
 * Provides methods for performing search queries, merging search results and aggregations,
 * and creating database-specific filters and sort parameters. Handles both local and
 * distributed search queries across multiple directories.
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

use GuzzleHttp\Client;
use GuzzleHttp\Promise\Utils;
use OCP\IURLGenerator;
use Symfony\Component\Uid\Uuid;

/**
 * Service for managing search operations and related functionalities.
 *
 * Provides methods for performing search queries, merging search results and aggregations,
 * and creating database-specific filters and sort parameters. Handles both local and
 * distributed search queries across multiple directories.
 */

class SearchService
{
    /** @var Client */
    public $client;

    /** @var array Base object configuration */
	public const BASE_OBJECT = [
		'database'   => 'objects',
		'collection' => 'json',
	];

    /**
     * SearchService constructor.
     *
     * @param ElasticSearchService $elasticService   The service responsible for handling interactions with the Elasticsearch database.
     * @param DirectoryService     $directoryService The service used for managing directory-related operations.
     * @param IURLGenerator        $urlGenerator     The service for generating URLs within the application.
     */
	public function __construct(
		private readonly ElasticSearchService $elasticService,
		private readonly DirectoryService $directoryService,
		private readonly IURLGenerator $urlGenerator
	) {
		$this->client = new Client();
	}

    /**
     * Merge facets from existing and new aggregations.
     *
     * @param array $existingAggregation
     * @param array $newAggregation
     * @return array Merged facets
     */
	public function mergeFacets(array $existingAggregation, array $newAggregation): array
	{
		$results = [];
		$existingAggregationMapped = [];
		$newAggregationMapped = [];

		// Map existing aggregation
		foreach ($existingAggregation as $value) {
			$existingAggregationMapped[$value['_id']] = $value['count'];
		}

		// Merge new aggregation with existing
		foreach ($newAggregation as $value) {
			if (isset ($existingAggregationMapped[$value['_id']]) === true) {
				$newAggregationMapped[$value['_id']] = $existingAggregationMapped[$value['_id']] + $value['count'];
			} else {
				$newAggregationMapped[$value['_id']] = $value['count'];
			}
		}

		// Combine results
		foreach (array_merge(array_diff($existingAggregationMapped, $newAggregationMapped), array_diff($newAggregationMapped, $existingAggregationMapped)) as $key => $value) {
			$results[] = ['_id' => $key, 'count' => $value];
		}

		return $results;
	}

    /**
     * Merge existing and new aggregations.
     *
     * @param array|null $existingAggregations
     * @param array|null $newAggregations
     * @return array Merged aggregations
     */
	private function mergeAggregations(?array $existingAggregations, ?array $newAggregations): array
	{
		if ($newAggregations === null) {
			return [];
		}

		foreach ($newAggregations as $key => $aggregation) {
			if (isset($existingAggregations[$key]) === false) {
				$existingAggregations[$key] = $aggregation;
			} else {
				$existingAggregations[$key] = $this->mergeFacets($existingAggregations[$key], $aggregation);
			}
		}
		return $existingAggregations;
	}

    /**
     * Comparison function for sorting result arrays.
     *
     * @param array $a
     * @param array $b
     * @return int
     */
	public function sortResultArray(array $a, array $b): int
	{
		return $a['_score'] <=> $b['_score'];
	}

    /**
     * Perform a search operation.
     *
     * @param array $parameters Search parameters
     * @param array $elasticConfig Elasticsearch configuration
     * @param array $dbConfig Database configuration
     * @param array $catalogi Catalogi configuration
     * @return array Search results
     */
	public function search(array $parameters, array $elasticConfig, array $dbConfig, array $catalogi = []): array
	{
		$localResults['results'] = [];
		$localResults['facets'] = [];

		$totalResults = 0;
		$limit = isset($parameters['_limit']) === true ? $parameters['_limit'] : 30;
		$page = isset($parameters['_page']) === true ? $parameters['_page'] : 1;

		// Perform local search if Elasticsearch is configured
		if ($elasticConfig['location'] !== '') {
			$localResults = $this->elasticService->searchObject(filters: $parameters, config: $elasticConfig, totalResults: $totalResults,);
		}

		$directory = $this->directoryService->listDirectory(limit: 1000);

		// Return early if directory is empty
		if (count($directory) === 0) {
			$pages   = (int) ceil($totalResults / $limit);
			return [
                'facets' => $localResults['facets'],
				'results' => $localResults['results'],
				'count' => count($localResults['results']),
				'limit' => (int) $limit,
				'page' => (int) $page,
				'pages' => $pages === 0 ? 1 : $pages,
				'total' => $totalResults
			];
		}

		$results = $localResults['results'];
		$aggregations = $localResults['facets'];

		$searchEndpoints = [];

		// Prepare search endpoints
		$promises = [];
		foreach ($directory as $instance) {
			if (
				$instance['default'] === false
				|| isset($parameters['_catalogi']) === true
				&& in_array($instance['catalog'], $parameters['_catalogi']) === false
				|| $instance['search'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"))
			) {
				continue;
			}
			$searchEndpoints[$instance['search']][] = $instance['catalog'];
		}

		unset($parameters['_catalogi']);

		// Perform asynchronous requests to search endpoints
		foreach ($searchEndpoints as $searchEndpoint => $catalogi) {
			$parameters['_catalogi'] = $catalogi;
			$promises[] = $this->client->getAsync($searchEndpoint, ['query' => $parameters]);
		}

		$responses = Utils::settle($promises)->wait();

		// Process responses
		foreach ($responses as $response) {
			if ($response['state'] === 'fulfilled') {
				$responseData = json_decode(
					json: $response['value']->getBody()->getContents(),
					associative: true
				);

				$results = array_merge(
					$results,
					$responseData['results']
				);

				usort($results, [$this, 'sortResultArray']);

				$aggregations = $this->mergeAggregations($aggregations, $responseData['facets']);
			}
		}

		$pages   = (int) ceil($totalResults / $limit);

		return [
            'facets' => $aggregations,
			'results' => $results,
			'count' => count($results),
			'limit' => (int) $limit,
			'page' => (int) $page,
			'pages' => $pages === 0 ? 1 : $pages,
			'total' => $totalResults
		];
	}

	/**
	 * This function creates a mongodb filter array.
	 *
	 * Also unsets _search in filters !
	 *
	 * @param array $filters 	    Query parameters from request.
	 * @param array $fieldsToSearch Database field names to filter/search on.
	 *
	 * @return array $filters
	 */
	public function createMongoDBSearchFilter(array $filters, array $fieldsToSearch): array
	{
        if (isset($filters['_search']) === true) {
			$searchRegex = ['$regex' => $filters['_search'], '$options' => 'i'];
			$filters['$or'] = [];

			foreach ($fieldsToSearch as $field) {
				$filters['$or'][] = [$field => $searchRegex];
			}

			unset($filters['_search']);
		}

		foreach ($filters as $field => $value) {
			if ($value === 'IS NOT NULL') {
				$filters[$field] = ['$ne' => null];
			}
			if ($value === 'IS NULL') {
				$filters[$field] = ['$eq' => null];
			}
		}

		return $filters;
	}

	/**
	 * This function creates mysql search conditions based on given filters from request.
	 *
	 * @param array      $filters 	     Query parameters from request.
	 * @param array      $fieldsToSearch Fields to search on in sql.
	 * @param array|null $searchParams   Search params for sql.
	 *
	 * @return array $searchConditions
	 */
	public function createMySQLSearchConditions(array &$filters, array $fieldsToSearch, ?array &$searchParams = []): array
	{
		$searchConditions = [];
		if (isset($filters['_search']) === true) {
			foreach ($fieldsToSearch as $field) {
                if (isset($searchConditions[0]) === true) {
                    $searchConditions[0] = $searchConditions[0] . " OR LOWER($field) LIKE :search";
                } else {
				    $searchConditions[] = "LOWER($field) LIKE :search";
                }
			}
            if (isset($searchConditions[0]) === true) {
                $searchConditions[0] = "($searchConditions[0])";
            }
		}

        foreach ($filters as $key => $value) {
            if ($key === '_search') {
                continue; // Skip _search field
            }

            // Skip if the filter value is empty or null
            if (empty($value) === true) {
                continue;
            }

            // Check if the filter value contains commas, meaning multiple values (OR conditions)
            if (is_string($value) === true && strpos($value, ',') !== false) {
                $values = explode(',', $value); // Split the comma-separated values into an array
                $orConditions = [];

                foreach ($values as $i => $val) {
                    $paramKey = "{$key}_$i"; // Generate unique parameter key
                    $orConditions[] = "$key = :$paramKey";
                    $searchParams[$paramKey] = trim($val); // Add each value to the search params
                }

                // Only add the condition if we have valid values to search for
                if (empty($orConditions) === false) {
                    $searchConditions[] = '(' . implode(' OR ', $orConditions) . ')';
                }

                // Unset to prevent sql errors because of double filtering.
                unset($filters[$key]);
            }
        }

        // Ensure that search conditions and params exist before proceeding
        if (empty($searchConditions) === true) {
            // Handle the case where no search conditions are generated
            $searchConditions[] = '1=1'; // Default condition to avoid breaking the SQL query
        }
		return $searchConditions;
	}

	/**
	 * This function unsets all keys starting with _ from filters.
	 *
	 * @param array $filters Query parameters from request.
	 *
	 * @return array $filters
	 */
	public function unsetSpecialQueryParams(array $filters): array
	{
        foreach ($filters as $key => $value) {
            if (str_starts_with($key, '_')) {
                unset($filters[$key]);
            }
			if ($key === 'search') {
				unset($filters[$key]);
			}
        }

		return $filters;
	}

	/**
	 * This function creates mysql search parameters based on given filters from request.
	 *
	 * @param array $filters 	    Query parameters from request.
	 *
	 * @return array $searchParams
	 */
	public function createMySQLSearchParams(array $filters): array
	{
		$searchParams = [];
		if (isset($filters['_search']) === true) {
			$searchParams['search'] = '%' . strtolower($filters['_search']) . '%';
		}

		return $searchParams;
	}

	/**
	 * This function creates an sort array based on given order param from request.
	 *
	 * @param array $filters Query parameters from request.
	 *
	 * @return array $sort
	 */
	public function createSortForMySQL(array $filters): array
	{
		$sort = [];
		if (isset($filters['_order']) && is_array($filters['_order'])) {
			foreach ($filters['_order'] as $field => $direction) {
				$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
				$sort[$field] = $direction;
			}
		}

		return $sort;
	}

	/**
	 * This function creates a sort array based on given order param from request.
	 *
	 * @todo Not tested yet. See PublicationsController->index()
	 *
	 * @param array $filters Query parameters from request.
	 *
	 * @return array $sort
	 */
	public function createSortForMongoDB(array $filters): array
	{
		$sort = [];
		if (isset($filters['_order']) && is_array($filters['_order'])) {
			foreach ($filters['_order'] as $field => $direction) {
				$sort[$field] = strtoupper($direction) === 'DESC' ? -1 : 1;
			}
		}

		return $sort;
	}

    /**
     * This function adds a single query param to the given $vars array. ?$name=$value
     * Will check if request query $name has [...] inside the parameter, like this: ?queryParam[$nameKey]=$value.
     * Works recursive, so in case we have ?queryParam[$nameKey][$anotherNameKey][etc][etc]=$value.
     * Also checks for queryParams ending on [] like: ?queryParam[$nameKey][] (or just ?queryParam[]), if this is the case
     * this function will add given value to an array of [queryParam][$nameKey][] = $value or [queryParam][] = $value.
     * If none of the above this function will just add [queryParam] = $value to $vars.
     *
     * @param array  $vars    The vars array we are going to store the query parameter in
     * @param string $name    The full $name of the query param, like this: ?$name=$value
     * @param string $nameKey The full $name of the query param, unless it contains [] like: ?queryParam[$nameKey]=$value
     * @param string $value   The full $value of the query param, like this: ?$name=$value
     *
     * @return void
     */
    private function recursiveRequestQueryKey(array &$vars, string $name, string $nameKey, string $value): void
    {
        $matchesCount = preg_match(pattern: '/(\[[^[\]]*])/', subject: $name, matches:$matches);
        if ($matchesCount > 0) {
            $key  = $matches[0];
            $name = str_replace(search: $key,  replace:'', subject: $name);
            $key  = trim(string: $key, characters: '[]');
            if (empty($key) === false) {
                $vars[$nameKey] = ($vars[$nameKey] ?? []);
                $this->recursiveRequestQueryKey(
                    vars: $vars[$nameKey],
                    name: $name,
                    nameKey: $key,
                    value: $value
                );
            } else {
                $vars[$nameKey][] = $value;
            }
        } else {
            $vars[$nameKey] = $value;
        }
    }

	/**
	 * Parses the request query string and returns it as an array of queries.
	 *
	 * @param string $queryString The input query string from the request.
	 *
	 * @return array The resulting array of query parameters.
	 */
	public function parseQueryString(string $queryString = ''): array
	{
        $vars = [];

		$pairs = explode(separator: '&', string: $queryString);
		foreach ($pairs as $pair) {
			$kvpair = explode(separator: '=', string: $pair);

			$key = urldecode(string: $kvpair[0]);
			if (empty($key) === true) {
				continue;
			}
			$value = '';
			if (count(value: $kvpair) === 2) {
				$value = urldecode(string: $kvpair[1]);
			}

			$this->recursiveRequestQueryKey(
				vars: $vars,
				name: $key,
                nameKey: explode(
                    separator: '[',
                    string: $key
                )[0],
				value: $value
			);
		}


		return $vars;
	}

}
