<?php
set_exception_handler( 'gwc_exception_handler' );

# Centralised Web controller - all sites will have $GWC available to them
$directory_path = str_replace('controllers','',__DIR__);
include_once($directory_path.'classes/green_web_controller.php');

$GWC = new green_web_controller();
$GWC->handleRequest();

function gwc_exception_handler($exception){
	$style = "body { background-color: #EFEFEF; color: #2E2F30; text-align: center; font-family: arial, sans-serif; }  div.dialog { width: 25em; margin: 4em auto 0 auto; border: 1px solid #CCC;  border-right-color: #999; border-left-color: #999; border-bottom-color: #BBB; border-top: #B00100 solid 4px; border-top-left-radius: 9px; border-top-right-radius: 9px; background-color: white; padding: 7px 4em 0 4em; }  h1 { font-size: 100%; color: #730E15; line-height: 1.5em; }  body > p { width: 33em; margin: 0 auto 1em; padding: 1em 0; background-color: #F7F7F7; border: 1px solid #CCC; border-right-color: #999; border-bottom-color: #999; border-bottom-left-radius: 4px; border-bottom-right-radius: 4px; border-top-color: #DADADA; color: #666; box-shadow:0 3px 8px rgba(50, 50, 50, 0.17); }";
	$error = $exception->getMessage();
	echo <<<EOL
<!DOCTYPE html>
<html>
<head>
<title>GWC - Critical Error</title>
<style>
$style
</style>
</head>

<body>
<div class="dialog">
<h1>An unexpected error has occured.</h1>
<p>Please try refreshing the page or come back later.</p>
</div>
<p>If you are an administrator of the site, please check the application logs for more information.<br/>The exception reported was [$error].</p>
</body>
</html>
EOL;
die();
}
?>
