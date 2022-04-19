<?php

Class green_template {
	
	private $name;
	private $logger;
	private $templateDir;
	private $headerTemplate;
	private $footerTemplate;
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
   		if(is_object($value)){
   			$this->logger->log('TEMPLATE: Adding variable ['.serialize($key).'] with value [Object]',LOG_LEVEL_VERBOSE);
   		} else {
   			$this->logger->log('TEMPLATE: Adding variable ['.serialize($key).'] with value ['.serialize($value).']',LOG_LEVEL_VERBOSE);
   		}
   		$this->vars[$key] = $value;
   	}
   	
   	public function setHeaderTemplate($filepath){
   		$this->logger->log('TEMPLATE: Global Header template ['.$filepath.']',LOG_LEVEL_VERBOSE);
   		$this->headerTemplate = $filepath;
   	}
   	
   	public function setFooterTemplate($filepath){
   		$this->logger->log('TEMPLATE: Global Footer template ['.$filepath.']',LOG_LEVEL_VERBOSE);
   		$this->footerTemplate = $filepath;
   	}
   	
   	public function render($path,$showHeaderAndFooter){
   		if(!empty($this->vars)){
   			extract($this->vars);
   		}
   		$template = $this->templateDir.$path;
   		$this->logger->log('TEMPLATE: Rendering of ['.$template.'] requested',LOG_LEVEL_VERBOSE);
   		if(!$showHeaderAndFooter){
   			$this->logger->log('TEMPLATE: NOT Rendering Header/Footer',LOG_LEVEL_VERBOSE);
   		}
   		if($showHeaderAndFooter && !empty($this->headerTemplate) && !file_exists($this->headerTemplate)){
   			$this->fail('Header Template ['.$this->headerTemplate.'] not found');
   		} elseif($showHeaderAndFooter && $this->headerTemplate) {
   			$this->logger->log('TEMPLATE: Rendering Header ['.$this->headerTemplate.']',LOG_LEVEL_VERBOSE);
   			try{
   				include($this->headerTemplate);
   			} catch(Exception $ex){
   				$this->logger->log('TEMPLATE: Error when rendering header  ['.$ex.']',LOG_LEVEL_NORMAL);
   			}
   		}
   		if(!file_exists($template)){
   			$this->fail('Template ['.$template.'] not found');
   		} else {
   			$this->logger->log('TEMPLATE: Rendering ['.$template.']',LOG_LEVEL_VERBOSE);
   			try{
   				include($template);
   			} catch(Exception $ex){
   				$this->logger->log('TEMPLATE: Error when rendering template  ['.$ex.']',LOG_LEVEL_NORMAL);
   			}
   		}
   		if($showHeaderAndFooter && !empty($this->footerTemplate) && !file_exists($this->footerTemplate)){
   			$this->fail('Footer Template ['.$this->footerTemplate.'] not found');
   		} elseif($showHeaderAndFooter && $this->footerTemplate) {
   			$this->logger->log('TEMPLATE: Rendering Footer ['.$this->footerTemplate.']',LOG_LEVEL_VERBOSE);
   			try{
   				$log_messages = $this->logger->getMessageBuffer();
   				include($this->footerTemplate);
   			} catch(Exception $ex){
   				$this->logger->log('TEMPLATE: Error when rendering footer  ['.$ex.']',LOG_LEVEL_NORMAL);
   			}
   		}
   	}
   	
   	private function fail($msg){
   		$this->logger->log('TEMPLATE: '.$msg);
   		throw new Exception($msg);
   	}
	
}
?>