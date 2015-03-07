<?php
#########################
## Main web controller to handle all web requests for all sites
## Drives creation of factory classes used by all sites
## TO DO - handle 4xx & 5xx responses too
##
#########################

Class green_web_controller {

	protected $LOGGER;
	protected $TEMPLATE;
	protected $DATABASE;
	protected $SESSION;
	private $factories = array('logger','template','database','session');	// become upper case properties
	private $siteName = 'unknown_site';

	function __construct() {
		
   	}
	
	public function handleRequest(){
		
		$this->detectSite();
		
		$this->createRequestObjects();
	}
	
	## Using the web request, detect the site
	private function detectSite(){
		if(!empty($_SERVER['HTTP_HOST'])){
			$this->siteName = $_SERVER['HTTP_HOST'];
		}
	}
	
	private function createRequestObjects(){
		## initialise our static objects
		foreach ($this->factories as $factory){
			$factoryClassName = 'green_'.$factory.'_factory';
			$staticObjectName = strtoupper($factory);
			include $factoryClassName.'.php';
			$this->{$staticObjectName} = $factoryClassName::create($this->siteName);
		}
		
		var_dump($this);
	}
	
	public function log($msg,$level=LOG_LEVEL_NORMAL){
		$this->LOGGER->log($msg,$level=LOG_LEVEL_NORMAL);
	}
	
	## Cleanup
	function __destruct() {
       ## Close session at this point, close log handle, close db TO DO
   }
};
?>