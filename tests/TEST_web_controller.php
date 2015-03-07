<?php
include_once ('TEST_bootstrap.php');
$directory_path = '/data/green_software/green_framework/';
include_once($directory_path.'classes/green_web_controller.php');

$_SERVER['HTTP_HOST'] = 'fake-site.co.uk';

try {
	$GREEN_WEB_CONTROLLER = new green_web_controller();
	$GREEN_WEB_CONTROLLER->handleRequest();
	
	# Test we can log
	$GREEN_WEB_CONTROLLER->log('Test we can log '.__FILE__);
	
	# Test we can initiate a session - TO DO
	# will need to check if session initialised , if not start it - use site name to prevent cross contamination
	$key = 'myValue';
	$value = 'AdrianIsCool';
	$GREEN_WEB_CONTROLLER->sessionPut($key,$value);
	$newValue = $GREEN_WEB_CONTROLLER->sessionGet($key);
	if($value !== $newValue){
		print ($value.' doesn\'t equal '.$newValue);
	} else {
		print 'Session value matches !';
	}
	
} catch(Exception $ex){
	die('Framework Exception: '.$ex);
}

?>