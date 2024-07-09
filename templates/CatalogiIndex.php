<?php

use OCP\Util;

$appId = OCA\OpenCatalogi\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-catalogiScript');
Util::addStyle($appId, 'main');
?>
<div id="catalogi"></div>