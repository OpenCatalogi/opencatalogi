<?php

use OCP\Util;

$appId = OCA\OpenCatalog\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-directoryScript');
Util::addStyle($appId, 'main');
?>
<div id="directory"></div>