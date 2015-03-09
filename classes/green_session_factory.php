<?php

Class green_session_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$logger,$args){
		include_once('green_session.php');
		$sessionObject = new green_session($name,$logger);
		if(isset($args['session_mock']) && $args['session_mock'] == true){
			$sessionObject->mock();
		} else {
			$sessionObject->start();
		}
		return $sessionObject;
	}
	
};

?>