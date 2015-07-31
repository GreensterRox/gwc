<?php

Class green_routing {
	
	private $routeController;
	private $knownRoutes;
	
	function __construct($logger,$config_file_path) {
		$this->logger = $logger;
		$this->extractConfig($config_file_path);
   	}
   	
   	private function extractConfig($config_file_path){
   		$this->knownRoutes = parse_ini_file($config_file_path);
   	}
   	
   	public function matchApplicationRoute($url){
   		$path = strtolower(parse_url($url, PHP_URL_PATH));
   		$this->logger->log(get_class()." Attempting to match application route for request path [".$path."] ",LOG_LEVEL_VERBOSE);
   		
   		# before we begin remove last slash
   		$requested_route = explode('/',$path);
   		$break = false;
   		# start at full path and keep contracting until a match is found
   		# this means app will match /messages/edit/ before it matches /messages
   		# handle /messages/edit/1234 & /messages/edit & /messages/edit/
   		for($i=count($requested_route)-1;$i>=0;$i--){
   			
   			$local_route = array();
   			# another loop that builds path locally
   			for($x=0;$x<=$i;$x++){
   				$local_route[] = $requested_route[$x];
   			}
   			
   			$route_pattern = implode('/',$local_route);
   			$this->logger->log(get_class().' Looking for ['.$route_pattern.']',LOG_LEVEL_VERBOSE);
   			
   			# check for match
   			foreach($this->knownRoutes as $route => $controller){
   				if($route_pattern == $route){
   					$this->routeController = $controller;
   					$this->logger->log(get_class().' Found matching route ['.$route_pattern.']',LOG_LEVEL_VERBOSE);
   					$break = true;
   					break;
   				}
   			}
   			
   			if($break){
   				break;
   			}
   		}
   		
   		if(empty($this->routeController)){
   			$this->logger->log(get_class().' No matching route found for ['.$path.']',LOG_LEVEL_VERBOSE);
   		}
   	}
   	
   	public function runApplicationRoute($GWC){
   		if(!empty($this->routeController)){
	   		require_once($this->routeController);
	   		$ConrollerClassName = substr(basename($this->routeController),0,-4);
	   		$ControllerObject = new $ConrollerClassName($GWC);
	   		$this->logger->log(get_class().' Running application Route ['.$this->routeController.']',LOG_LEVEL_VERBOSE);
	   		$ControllerObject->run();
	   		exit;
	   	}
   	}
   	
}

?>