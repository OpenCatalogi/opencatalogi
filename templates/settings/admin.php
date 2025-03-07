<?php
use OCP\Util;

$appId = OCA\OpenCatalogi\AppInfo\Application::APP_ID;
Util::addScript($appId, $appId . '-settings-admin');
Util::addStyle($appId, 'main');

?>

<div id="admin-settings"></div>