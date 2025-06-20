<?php

return [
	'routes' => [
		/**
		 * Here we have the private endpoints, the part of the API that is used by the backend and not publicly accessible
		 */
		// Dashboard
		['name' => 'dashboard#index', 'url' => '/index', 'verb' => 'GET'],
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'], // Should be in directory becouse its public
		// Catalogi
		['name' => 'catalogi#index', 'url' => '/api/catalogi', 'verb' => 'GET'], // Public endpoint for getting all catalogs
		['name' => 'catalogi#show', 'url' => '/api/catalogi/{id}', 'verb' => 'GET'],
		// Global Configuration
		['name' => 'settings#index', 'url' => '/api/settings', 'verb' => 'GET'],
		['name' => 'settings#create', 'url' => '/api/settings', 'verb' => 'POST'],
		['name' => 'settings#load', 'url' => '/api/settings/load', 'verb' => 'GET'],
		/**
		 * And here we have the public endpoints, the part of the API that is used by the frontend and publicly accessible
		 */		
		// Publications
		['name' => 'publications#index', 'url' => '/api/publications', 'verb' => 'GET'],
		['name' => 'publications#show', 'url' => '/api/publications/{id}', 'verb' => 'GET'],
		['name' => 'publications#uses', 'url' => '/api/publications/{id}/uses', 'verb' => 'GET'],
		['name' => 'publications#used', 'url' => '/api/publications/{id}/used', 'verb' => 'GET'],
		['name' => 'publications#attachments', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		['name' => 'publications#download', 'url' => '/api/publications/{id}/download', 'verb' => 'GET'],
		// Glossary
		['name' => 'glossary#index', 'url' => '/api/glossary', 'verb' => 'GET'],
		['name' => 'glossary#show', 'url' => '/api/glossary/{id}', 'verb' => 'GET'],
		// Themes
		['name' => 'themes#index', 'url' => '/api/themes', 'verb' => 'GET'],
		['name' => 'themes#show', 'url' => '/api/themes/{id}', 'verb' => 'GET'],
		// Menus
		['name' => 'menus#index', 'url' => '/api/menus', 'verb' => 'GET'],
		['name' => 'menus#show', 'url' => '/api/menus/{id}', 'verb' => 'GET'],
		// Pages
		['name' => 'pages#index', 'url' => '/api/pages', 'verb' => 'GET'],
		['name' => 'pages#show', 'url' => '/api/pages/{slug}', 'verb' => 'GET', 'requirements' => ['slug' => '.+']],
		// Directory
		['name' => 'listings#synchronise', 'url' => '/api/listings/synchronise/{id?}', 'verb' => 'POST'],
		['name' => 'listings#index', 'url' => '/api/listings', 'verb' => 'GET'],
		['name' => 'listings#show', 'url' => '/api/listings/{id}', 'verb' => 'GET'],
		['name' => 'listings#create', 'url' => '/api/listings', 'verb' => 'POST'],
		['name' => 'listings#update', 'url' => '/api/listings/{id}', 'verb' => 'PUT'],
		['name' => 'listings#destroy', 'url' => '/api/listings/{id}', 'verb' => 'DELETE'],
		['name' => 'listings#add', 'url' => '/api/listings/add', 'verb' => 'POST'],
		['name' => 'directory#index', 'url' => '/api/directory', 'verb' => 'GET'],
		['name' => 'directory#show', 'url' => '/api/directory/{id}', 'verb' => 'GET'],
		['name' => 'directory#update', 'url' => '/api/directory', 'verb' => 'POST'],
		['name' => 'directory#publicationType', 'url' => '/api/directory/publication_types/{id}', 'verb' => 'GET'],
		// Search
		['name' => 'search#index', 'url' => '/api/search', 'verb' => 'GET'],
	]
];
