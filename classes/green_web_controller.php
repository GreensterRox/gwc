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
	private $root;
	private $factories = array('logger','template','database','session');
	private $siteName = 'unknown_site';
	

	function __construct() {
		
   	}
	
	public function handleRequest($objects=array()){
		$this->setRoot();
		$this->detectSite();
		if(!empty($objects)){
			// basically allows me to test in isolation (but we always need the logger!)
			$this->createLogger();
			if(isset($objects['template'])){
				$this->createTemplate();
			}
			if(isset($objects['database'])){
				$this->createDatabase();
			}
			if(isset($objects['session'])){
				$this->createSession();
			}
		} else {
			$this->createRequestObjects();
		}
	}
	
	## Using the web request, detect the site
	private function detectSite(){
		if(empty($_SERVER['HTTP_HOST'])){
			throw new Exception ('Unable to detect site host');
		}
		$this->siteName = $_SERVER['HTTP_HOST'];
		$this->loadProps($this->siteName);
	}
	
	private function loadProps($name){
		$siteConfig = $this->root.'/sites/'.$name.'.config.php';
		if(!file_exists($siteConfig)){
			throw new Exception('Cannot find site config file here: '.$this->root.'/sites/'.$name.'.config.php');
		}
		include($siteConfig);
		$this->args = $CONFIG;
		$this->debug = (isset($this->args['options']['debug']) ? $this->args['options']['debug'] : false);
	}
	
	private function setRoot(){
		$this->root = str_replace('classes/green_web_controller.php','',__FILE__);
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
			##return LOG_LEVEL_OUT;
			return LOG_LEVEL_VERBOSE;
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
	
	public function templatePut($key,$value){
		$this->TEMPLATE->addVar($key,$value);
	}
	
	public function render($path){
		return $this->TEMPLATE->render($path);
	}
	
	public function DBRead($sql,$params=array()){
		try {
			return $this->DATABASE->read($sql,$params);
		} catch(Exception $ex){
			return $this->LOGGER->log("Cannot Read from Database: ".$ex->getMessage(),$level=LOG_LEVEL_NORMAL);
		}
	}
	
	public function DBWrite($sql){
		try{
			return $this->DATABASE->write($sql);
		} catch(Exception $ex){
			return $this->LOGGER->log("Cannot Write to Database: ".$ex->getMessage(),$level=LOG_LEVEL_NORMAL);
		}
	}
	
	public function DBStartTransaction(){
		$this->DATABASE->startTransaction();
	}
	
	public function DBCommit(){
		$this->DATABASE->commit();
	}
	
	public function DBRollback(){
		$this->DATABASE->rollback();
	}
	
	## Cleanup
	function __destruct() {
       ## Close session at this point, close log handle, close db TO DO
   }
};
?>