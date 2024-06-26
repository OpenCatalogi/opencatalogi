<?php

return [
    'resources' => [
        'metadata' => ['url' => '/metadata/api'],
        'publications' => ['url' => '/publications/api'],
        'catalogi' => ['url' => '/catalogi/api'],
        'directory' => ['url' => '/directory/api']
    ],
	'routes' => [
		['name' => 'dashboard#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'metadata#page', 'url' => '/metadata', 'verb' => 'GET'],
		['name' => 'publications#index', 'url' => '/publications', 'verb' => 'GET'],
		['name' => 'catalogi#index', 'url' => '/catalogi', 'verb' => 'GET'],
		['name' => 'search#index', 'url' => '/search', 'verb' => 'GET'],
		['name' => 'directory#index', 'url' => '/directory', 'verb' => 'GET'],
        ['name' => 'configuration#index', 'url' => '/configuration', 'verb' => 'GET'],
        ['name' => 'configuration#create', 'url' => '/configuration', 'verb' => 'POST']		
	],
];
