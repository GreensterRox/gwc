<?php
#########################
## Main GWC web controller to handle all web requests for all sites
## Drives creation of factory classes used by all sites
## TO DO - handle 4xx & 5xx responses too
##
#########################
define ('LOG_LEVEL_VERBOSE',1);
define ('LOG_LEVEL_NORMAL',2);
define ('LOG_LEVEL_NONE',3);	# why would you ever need this ??
define ('LOG_LEVEL_OUT',99);

Class green_web_controller {

	protected $LOGGER;
	protected $TEMPLATE;
	protected $DATABASE;
	protected $SESSION;
	private $args;
	private $debug;
	private $factories = array('logger','template','database','session');	// become upper case properties / logger must be first
	private $siteName = 'unknown_site';
	

	function __construct($debug=false) {
		$this->debug = $debug;
   	}
	
	public function handleRequest($args=array()){
		
		$this->args = $args;
		
		$this->detectSite();
		
		$this->createRequestObjects();
	}
	
	private function parseArgs($args){
		if(isset($args['session_args'])){
			$this->session_args = $args['session_args'];
		}
	}
	
	## Using the web request, detect the site
	private function detectSite(){
		if(!empty($_SERVER['HTTP_HOST'])){
			$this->siteName = $_SERVER['HTTP_HOST'];
		}
	}
	
	private function createRequestObjects(){
		$this->createLogger();
		$this->createSession();
		$this->createTemplate();
		$this->createDatabase();
	}
	
	private function createLogger(){
		include_once 'green_logger_factory.php';
		if(isset($this->args['logger'])){
			$args = $this->args['logger'];
		} else {
			$args=array();
		}
		$logLevel = $this->getLogLevel($this->debug);
		$this->LOGGER = green_logger_factory::create($this->siteName,$logLevel,$args);
	}	
		
	private function createSession(){
		include_once 'green_session_factory.php';
		if(isset($this->args['session'])){
			$args = $this->args['session'];
		} else {
			$args=array();
		}
		$this->SESSION = green_session_factory::create($this->siteName,$this->LOGGER,$args);
	}
	
	private function createTemplate(){
		include_once 'green_template_factory.php';
		if(isset($this->args['template'])){
			$args = $this->args['template'];
		} else {
			$args=array();
		}
		$this->TEMPLATE = green_template_factory::create($this->siteName,$this->LOGGER,$args);
	}
	
	private function createDatabase(){
		include_once 'green_database_factory.php';
		if(isset($this->args['database'])){
			$args = $this->args['database'];
		} else {
			$args=array();
		}
		$this->DATABASE = green_database_factory::create($this->siteName,$this->LOGGER,$args);
	}
	
	
	private function getLogLevel($debug){
		if($debug){
			return LOG_LEVEL_VERBOSE;	## to do these are in logger class not facotry class - how do we preload this crap (put them in GWC class ?!)
		} else {
			return LOG_LEVEL_NORMAL;
		}
	}
	
	public function log($msg,$level=LOG_LEVEL_NORMAL){
		return $this->LOGGER->log($msg,$level=LOG_LEVEL_NORMAL);
	}
	
	public function sessionPut($key,$value){
		$this->SESSION->put($key,$value);
	}
	
	public function sessionGet($key){
		return $this->SESSION->get($key);
	}
	
	public function sessionId(){
		return $this->SESSION->getSessionId();
	}
	
	## Cleanup
	function __destruct() {
       ## Close session at this point, close log handle, close db TO DO
   }
};
?>