<?php

Class green_database_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$logger,$args){
		$technology = 'mysql';
		# optional args
		if(isset($args['technology'])){
			$technology = $args['technology'];
		}
		# required args
		foreach(array('server','name','username','password') as $required){
			if(!isset($args[$required])){
				throw new Exception ('You must supply a value for ['.$required.'] in your site config');
			}
		}
		
		switch($technology){
			case 'mysql':
				$className = 'green_database_mysql';
				include_once($className.'.php');
				$DB = new green_database_mysql($logger);
				$DB->connect($args['server'],$args['name'],$args['username'],$args['password']);
				return $DB;
				break;
			default:
				throw new Exception('Unsupported database type ['.$technology.']');
				break;
		}
	}
	
};

?>