<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

\OC_App::loadApp(OCA\OpenCatalogi\AppInfo\Application::APP_ID);
OC_Hook::clear();
