<?php

return [
    'resources' => [
        'metadata' => ['url' => '/api/metadata'],
        'publications' => ['url' => '/api/publications'],
        'organisations' => ['url' => '/api/organisations'],
        'themes' => ['url' => '/api/themes'],
        'attachments' => ['url' => '/api/attachments'],
        'catalogi' => ['url' => '/api/catalogi'],
        'directory' => ['url' => '/api/directory']
    ],
	'routes' => [
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		['name' => 'metadata#page', 'url' => '/metadata', 'verb' => 'GET'],
		['name' => 'publications#page', 'url' => '/publications', 'verb' => 'GET'],
		['name' => 'catalogi#page', 'url' => '/catalogi', 'verb' => 'GET'],
		['name' => 'search#index', 'url' => '/search', 'verb' => 'GET'],
		['name' => 'search#index', 'url' => '/api/search', 'verb' => 'GET'],
		['name' => 'search#show', 'url' => '/api/search/{id}', 'verb' => 'GET'],
		['name' => 'directory#page', 'url' => '/directory', 'verb' => 'GET'],
		['name' => 'directory#add', 'url' => '/api/directory/add', 'verb' => 'POST'],
        ['name' => 'configuration#index', 'url' => '/configuration', 'verb' => 'GET'],
        ['name' => 'configuration#create', 'url' => '/configuration', 'verb' => 'POST']
	],
];
