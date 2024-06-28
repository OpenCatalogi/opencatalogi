<?php

use OCP\Util;

$appId = OCA\OpenCatalog\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-searchScript');
Util::addStyle($appId, 'main');
?>
<div id="search"></div>