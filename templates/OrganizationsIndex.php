<?php

use OCP\Util;

$appId = OCA\OpenCatalogi\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-organizationsScript');
Util::addStyle($appId, 'main');
?>
<div id="organizations"></div>