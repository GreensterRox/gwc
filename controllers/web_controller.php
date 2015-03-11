<?php
# TO DO: Could we utilise a global exceptin handler?
# set_exception_handler( array( __CLASS__, 'safe_exception' ) );

# Centralised Web controller - all sites will have $GWC available to them
# TO DO Use __FILE__ to figure out where main class lives
$directory_path = '/data/green_software/green_framework/';
include_once($directory_path.'classes/green_web_controller.php');
try {
	$GWC = new green_web_controller();
	$GWC->handleRequest();
} catch(Exception $ex){
	die('need an elegant way of handling this - hard coded template or somming, etc');
}
?>