<?php
#########################
## Main GWC web controller to handle all web requests for all sites
## Drives creation of factory classes used by all sites
## TODO - handle 4xx & 5xx responses
## TODO - Template Header & Footer
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
	private $PRE_RUNNERS = array();
	private $whitelistResource = false;

	function __construct() {
		
   	}
	
	public function handleRequest($objects=array()){
		$this->setRoot();
		$this->detectSite();
		$this->createLogger();
		$this->flagIfWhitelistResource();
		# only create the GWC framework if no whitelist resource match
		if(!$this->whitelistResource){
			if(!empty($objects)){
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
			$this->handle_auto_prepend_files();
		} else {
			$this->log(get_class().' Whitelist resource ! Not creating GWC framework',LOG_LEVEL_VERBOSE);
		}
	}
	
	# Sets a flag if a whitelist type reosurce is detected
	# prevents us from creating DB connections, sessions for images, css, etc
	private function flagIfWhitelistResource(){
		$url = $_SERVER['SCRIPT_URL'];
		
   		$patterns = array(
							'#^/css#',
							'#^/js#',
							'#^/images#'
							);
		foreach($patterns as $pattern){
			$this->log(get_class().' Checking url ['.$url.'] for whitelist pattern ['.$pattern.'].',LOG_LEVEL_VERBOSE);
			preg_match($pattern, $url, $matches);
			if(count($matches)){
				$this->log(get_class().' whitelist pattern found ! ['.$matches[0].']',LOG_LEVEL_VERBOSE);
				$this->whitelistResource = TRUE;
				break;
			} else {
				$this->log(get_class().' whitelist pattern not found ['.$pattern.']',LOG_LEVEL_VERBOSE);
			}
		}
		
	}
	
	public function isWhitelistRequest(){
		return $this->whitelistResource;
	}
	
	private function handle_auto_prepend_files(){
		if(isset($this->args['auto_prepend']) && is_array($this->args['auto_prepend'])){
			$GWC=$this;
			foreach($this->args['auto_prepend'] as $object => $subData){
				foreach($subData as $key => $value){
					$this->_addToPreRunners($object,$key,$value);
					if($key == 'include_file'){
						require_once($value);
						continue;
					}
					if($key == 'object_to_retrieve' && isset($$value)){
						$this->$value = $$value;
						continue;
					}
				}
			}
			$this->handle_pre_runners();
		}
	}
	
	# Handle any prepended objects that need to be run
	private function handle_pre_runners(){
		foreach($this->PRE_RUNNERS as $object_name => $data){
			if(isset($data['run_method']) && !empty($data['run_method'])){
				if(isset($this->$object_name) && is_object($this->$object_name)){
					$this->$object_name->$data['run_method']();
				}
			}
		}
	}
	
	private function _addToPreRunners($object,$key,$value){
		if(!isset($this->PRE_RUNNERS[$object])){
			$this->PRE_RUNNERS[$object] = array();
		}
		$this->PRE_RUNNERS[$object][$key] = $value;
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
			$this->LOGGER->log("Cannot Read from Database: ".$ex->getMessage(),$level=LOG_LEVEL_NORMAL);
			return false;
		}
	}
	
	public function DBWrite($sql,$params=array()){
		try{
			return $this->DATABASE->write($sql,$params);
		} catch(Exception $ex){
			$this->LOGGER->log("Cannot Write to Database: ".$ex->getMessage(),$level=LOG_LEVEL_NORMAL);
			return false;
		}
	}
	
	public function DBLastInsertID(){
		try{
			return $this->DATABASE->lastInsertID();
		} catch(Exception $ex){
			$this->LOGGER->log("Cannot Retrieve Last Insert ID from Database: ".$ex->getMessage(),$level=LOG_LEVEL_NORMAL);
			return false;
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
       ## Close session at this point, close log handle, close db TODO
   }
};
?>