<?php

Class green_database_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$technology,$logger,$args){
		switch($technology){
			case 'mysql':
				$className = 'green_database_mysql';
				include_once($className.'.php');
				break;
			default '':
				throw new Exception('Unsupported database type ['.$technology.']');
				break;
		}
		
		return ($className($name,$logger);
	}
	
};

?>