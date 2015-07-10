<?php
set_exception_handler( 'gwc_exception_handler' );

# Centralised Web controller - all sites will have $GWC available to them
$directory_path = str_replace('controllers','',__DIR__);
include_once($directory_path.'classes/green_web_controller.php');
try {
	$GWC = new green_web_controller();
	$GWC->handleRequest();
} catch(Exception $ex){
	die('need an elegant way of handling this - hard coded template or somming, etc ['.$ex.']');
}

function gwc_exception_handler($exception){
	die('Unhandled exception caught. Need an elegant way of handling this - hard coded template or somming, etc ['.$exception->getMessage().']');
}
?>
