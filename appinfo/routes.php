<?php

return [
	'resources' => [
		'publication_types' => ['url' => '/api/publication_types'],
		'publications' => ['url' => '/api/publications'],
		'organizations' => ['url' => '/api/organizations'],
		'themes' => ['url' => '/api/themes'],
		'pages' => ['url' => '/api/pages'],
		'menus' => ['url' => '/api/menu'],
		'catalogi' => ['url' => '/api/catalogi'],
		'listings' => ['url' => '/api/listings'],
	],
	'routes' => [
		// Directory
		['name' => 'listing#synchronise', 'url' => '/api/listings/synchronise/{id?}', 'verb' => 'POST'],
		['name' => 'directory#index', 'url' => '/api/directory', 'verb' => 'GET'],
		['name' => 'directory#show', 'url' => '/api/directory/{id}', 'verb' => 'GET', 'requirements' => ['path' => '.+']],
		['name' => 'directory#update', 'url' => '/api/directory', 'verb' => 'POST'],
		['name' => 'directory#publicationType', 'url' => '/api/directory/publication_types/{id}', 'verb' => 'GET'], // Should be in directory becouse its public
		// Publication
		['name' => 'publication_types#synchronise', 'url' => '/api/publication_types/synchronise', 'verb' => 'POST'],
		// Dashboard
		['name' => 'dashboard#index', 'url' => '/index', 'verb' => 'GET'],
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		// Publications
		['name' => 'publications#attachments', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		['name' => 'publications#download', 'url' => '/api/publications/{id}/download', 'verb' => 'GET'],
		// Attachments
		['name' => 'attachments#create', 'url' => '/api/attachments', 'verb' => 'POST'],
		// user Settings & Global Configuration
		['name' => 'settings#index', 'url' => '/settings', 'verb' => 'GET'],
		['name' => 'settings#create', 'url' => '/settings', 'verb' => 'POST'],
		['name' => 'configuration#index', 'url' => '/configuration', 'verb' => 'GET'],
		['name' => 'configuration#update', 'url' => '/configuration', 'verb' => 'PUT'],
		// Search
		['name' => 'search#preflighted_cors', 'url' => '/api/search/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'search#index', 'url' => '/api/search', 'verb' => 'GET'],
		['name' => 'search#publications', 'url' => '/api/search/publications', 'verb' => 'GET'],
		['name' => 'search#publication', 'url' => '/api/search/publications/{publicationId}', 'verb' => 'GET', 'requirements' => ['publicationId' => '[^/]+']],
		['name' => 'search#attachments', 'url' => '/api/search/publications/{publicationId}/attachments', 'verb' => 'GET', 'requirements' => ['publicationId' => '[^/]+']],
		['name' => 'search#themes', 'url' => '/api/search/themes', 'verb' => 'GET'],
		['name' => 'search#theme', 'url' => '/api/search/themes/{themeId}', 'verb' => 'GET', 'requirements' => ['themeId' => '\d+']],
		['name' => 'search#pages', 'url' => '/api/public/pages', 'verb' => 'GET'],
		['name' => 'search#page', 'url' => '/api/public/pages/{pageSlug}', 'verb' => 'GET', 'requirements' => ['pageId' => '.+']],
		['name' => 'search#menu', 'url' => '/api/public/menu', 'verb' => 'GET'],
		
		// Object API routes
		['name' => 'objects#index', 'url' => 'api/objects/{objectType}', 'verb' => 'GET'],
		['name' => 'objects#create', 'url' => 'api/objects/{objectType}', 'verb' => 'POST'],
		['name' => 'objects#show', 'url' => 'api/objects/{objectType}/{id}', 'verb' => 'GET'],
		['name' => 'objects#update', 'url' => 'api/objects/{objectType}/{id}', 'verb' => 'PUT'],
		['name' => 'objects#destroy', 'url' => 'api/objects/{objectType}/{id}', 'verb' => 'DELETE'],
		['name' => 'objects#lock', 'url' => 'api/objects/{objectType}/{id}/lock', 'verb' => 'POST'],
		['name' => 'objects#unlock', 'url' => 'api/objects/{objectType}/{id}/unlock', 'verb' => 'POST'],
		['name' => 'objects#revert', 'url' => 'api/objects/{objectType}/{id}/revert', 'verb' => 'POST'],

		// Subdata operations under objects
		['name' => 'objects#getAuditTrail', 'url' => 'api/objects/{objectType}/{id}/audit', 'verb' => 'GET'],
		['name' => 'objects#getRelations', 'url' => 'api/objects/{objectType}/{id}/relations', 'verb' => 'GET'],
		['name' => 'objects#getUses', 'url' => 'api/objects/{objectType}/{id}/uses', 'verb' => 'GET'],
		
		// Files operations under objects
		['name' => 'objects#indexFiles', 'url' => 'api/objects/{objectType}/{id}/files', 'verb' => 'GET'],
		['name' => 'objects#createFile', 'url' => 'api/objects/{objectType}/{id}/files', 'verb' => 'POST'],
		['name' => 'objects#createFileMultipart', 'url' => 'api/objects/{objectType}/{id}/filesMultipart', 'verb' => 'POST'],
		['name' => 'objects#publishFile', 'url' => 'api/objects/{objectType}/{id}/publish/files/{filePath}', 'verb' => 'POST'],
		['name' => 'objects#depublishFile', 'url' => 'api/objects/{objectType}/{id}/files/depublish/{filePath}', 'verb' => 'POST'],
		['name' => 'objects#showFile', 'url' => 'api/objects/{objectType}/{id}/files/{filePath}', 'verb' => 'GET'],
		['name' => 'objects#updateFile', 'url' => 'api/objects/{objectType}/{id}/files/{filePath}', 'verb' => 'POST'],
		['name' => 'objects#deleteFile', 'url' => 'api/objects/{objectType}/{id}/files/{filePath}', 'verb' => 'DELETE'],
		
		// Tags
		['name' => 'objects#getAllTags', 'url' => 'api/tags', 'verb' => 'GET'],
	]
];
