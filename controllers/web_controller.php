<?php
# Centralised Web controller - all sites will have $GREEN_WEB_CONTROLLER available to them
$directory_path = '/data/green_software/green_framework/';
include_once($directory_path.'classes/green_web_controller.php');
try {
	$GWC = new green_web_controller();
	$GWC->handleRequest();
} catch(Exception $ex){
	die('need an elegant way of handling this - hard coded template or somming, etc');
}
?>