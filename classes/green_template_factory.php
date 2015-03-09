<?php

Class green_template_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$logger,$args){
		include_once('green_template.php');
		return new green_template($name,$logger,$args['root_dir']);
	}
	
};

?>