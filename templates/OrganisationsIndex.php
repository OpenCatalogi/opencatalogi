<?php

use OCP\Util;

$appId = OCA\OpenCatalogi\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-organisationsScript');
Util::addStyle($appId, 'main');
?>
<div id="organisations"></div>