<?php

Class green_logger_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$logLevel,$args){
		include_once('green_logger.php');
		return new green_logger($name,$logLevel);
	}
	
};

?>