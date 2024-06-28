<?php

use OCP\Util;

$appId = OCA\OpenCatalog\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-catalogiScript');
Util::addStyle($appId, 'main');
?>
<div id="catalogi"></div>