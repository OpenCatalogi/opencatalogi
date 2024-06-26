<?php

use OCP\Util;

$appId = OCA\OpenCatalog\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-publicationsScript');
Util::addStyle($appId, 'main');
?>
<div id="publications"></div>