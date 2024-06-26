<?php

use OCP\Util;

$appId = OCA\OpenCatalog\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-metaDataScript');
Util::addStyle($appId, 'main');
?>
<div id="metaData"></div>