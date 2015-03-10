<?php

Class green_template {
	
	private $name;
	private $logger;
	private $templateDir;
	private $vars;
	
	function __construct($name,$logger,$templateDir) {
		$this->logger = $logger;
		$this->name = $name;
		if(empty($templateDir)){
			$this->fail('No template directory configured for this site ['.$name.']');
		}
		$this->templateDir = $templateDir;
		$this->logger->log('TEMPLATE: Initialising template engine using root directory ['.$templateDir.']',LOG_LEVEL_VERBOSE);
   	}
   	
   	public function addVar($key,$value){
   		$this->logger->log('TEMPLATE: Adding variable ['.$key.'] with value ['.$value.']',LOG_LEVEL_VERBOSE);
   		$this->vars[$key] = $value;
   	}
   	
   	public function render($path){
   		if(!empty($this->vars)){
   			extract($this->vars);
   		}
   		$template = $this->templateDir.$path;
   		$this->logger->log('TEMPLATE: Rendering ['.$template.']',LOG_LEVEL_VERBOSE);
   		include($template);
   	}
   	
   	private function fail($msg){
   		$this->logger->log('TEMPLATE: '.$msg);
   		throw new Exception($msg);
   	}
	
}
?>