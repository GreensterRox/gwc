<?php

define ('LOG_LEVEL_VERBOSE',1);
define ('LOG_LEVEL_NORMAL',2);
define ('LOG_LEVEL_NONE',3);
define ('LOG_LEVEL_TEMPLATE_FOOTER',4);
define ('LOG_LEVEL_SYSOUT',5);

Class green_logger_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$args){
		include_once('green_logger.php');
		return new green_logger($name,$args);
	}
	
};

?>