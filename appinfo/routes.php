<?php

return [
    'resources' => [
        'metadata' => ['url' => '/metadata/api'],
        'publications' => ['url' => '/publications/api'],
        'catalogi' => ['url' => '/catalogi/api'],
        'directory' => ['url' => '/directory/api']
    ],
	'routes' => [
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		['name' => 'metadata#page', 'url' => '/metadata', 'verb' => 'GET'],
		['name' => 'metadata#metadata', 'url' => '/metadata/{id}', 'verb' => 'GET'],
		['name' => 'publications#page', 'url' => '/publications', 'verb' => 'GET'],
		['name' => 'publications#publication', 'url' => '/publication/{id}', 'verb' => 'GET'],
		['name' => 'publications#catalog', 'url' => '/catalog/{id}', 'verb' => 'GET'],
		['name' => 'catalogi#page', 'url' => '/catalogi', 'verb' => 'GET'], 
		['name' => 'search#page', 'url' => '/search', 'verb' => 'GET'],
		['name' => 'directory#page', 'url' => '/directory', 'verb' => 'GET'],
		['name' => 'directory#directory', 'url' => '/directory/{id}', 'verb' => 'GET'],
        ['name' => 'configuration#index', 'url' => '/configuration', 'verb' => 'GET'],
        ['name' => 'configuration#create', 'url' => '/configuration', 'verb' => 'POST']		
	],
];
