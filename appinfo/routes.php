<?php

return [
	'routes' => [
		['name' => 'dashboard#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'metadata#index', 'url' => '/metadata', 'verb' => 'GET'],
		['name' => 'publications#index', 'url' => '/publications', 'verb' => 'GET'],
		['name' => 'catalogi#index', 'url' => '/catalogi', 'verb' => 'GET'],
		['name' => 'search#index', 'url' => '/search', 'verb' => 'GET'],
		['name' => 'directory#index', 'url' => '/directory', 'verb' => 'GET']
		
	],
];
