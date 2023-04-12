<?php
#########################
## Main GWC web controller to handle all web requests for all sites
## Drives creation of factory classes used by all sites
## TODO - handle 4xx & 5xx responses
## TODO - Template Header & Footer
##
#########################

Class green_web_controller {

	protected $LOGGER;
	protected $TEMPLATE;
	protected $DATABASE;
	protected $SESSION;
	private $args;
	private $root;
	private $factories = array('logger','template','database','session');
	private $siteName = 'unknown_site';
	private $PRE_RUNNERS = array();
	private $whitelistResource = false;
	private $CSRF_protection = false;
	private $lastDBError = false;

	function __construct() {

   	}

	public function handleRequest($objects=array()){
		$this->setRoot();
		$this->detectSite();
		$this->createLogger();
		$this->flagIfWhitelistResource();
		# only create the GWC framework if no whitelist resource match
		if($this->whitelistResource){
			$this->send404(get_class().' Whitelist resource ! Resource Not Found ! Not creating GWC framework');
		} else {
			if(!empty($objects)){
				if(isset($objects['logger'])){	// allows me to override logger specifically
					if(!empty($objects['logger'])){
						$this->args['logger']=$objects['logger'];
						$this->createLogger();
					}
				}
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
			$this->handleOptions();

			$this->handle_plugins();

			$this->handleURLRouting();
		}
	}

	private function handleOptions(){
		if(isset($this->args['options']['CSRF_protection']) && ($this->args['options']['CSRF_protection'] == TRUE)){
			$this->CSRF_protection = true;
			$this->handleNoCSRF();
		}
	}

	private function handleNoCSRF(){
		require_once('green_nocsrf.php');
		# first look for forms
		if(!empty($_POST) && count($_POST) > 0){
			try {
				NoCSRF::check( 'gwc_csrf', $_POST, true, 60*10, false );
			}
			catch (Exception $e){
				throw new Exception ('Invalid form request detected');
			}
		}

		# generate token
		$this->CSRF_TOKEN = NoCSRF::generate( 'gwc_csrf' );
	}

	public function CSRF_protection(){
		if($this->CSRF_protection){
			return '<input type="hidden" name="gwc_csrf" value="'.$this->CSRF_TOKEN.'">';
		}
	}

	# Handles User friendly URL Routing
	# if switched on at config level
	private function handleURLRouting(){
		if(isset($this->args['routing']['on']) && $this->args['routing']['on'] === TRUE){
			if(isset($this->args['routing']['routes_file'])){
				if(is_file($this->args['routing']['routes_file'])){
					# we're good to go
					require_once 'green_routing.php';
					$URL_Router = new green_routing($this->LOGGER,$this->args['routing']['routes_file']);
					$route_found = $URL_Router->matchApplicationRoute($_SERVER['SCRIPT_URI']);
					if($route_found){
						$URL_Router->runApplicationRoute($this);
					} else {
						$this->send404(get_class().' Not a valid route');
					}
				} else {
					throw new Exception('Routes config file cannot be read by GWC framework ['.$this->args['routing']['routes_file'].']');
				}
			} else {
				throw new Exception('You must config a routes file location if you wish to use URL routing');
			}
		}
	}

	private function send404($msg){
		$this->log($msg,LOG_LEVEL_VERBOSE);
		header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found",TRUE,404);
		echo "<h1>NOT FOUND</h1>";
		exit;
	}

	# Sets a flag if a whitelist type reosurce is detected
	# prevents us from creating DB connections, sessions for broken images, css, etc (as everything is routed !)
	private function flagIfWhitelistResource(){
		if(isset($_SERVER['SCRIPT_URL'])){
			$url = $_SERVER['SCRIPT_URL'];

	   		$patterns = array(
								'#^/css#',
								'#^/js#',
								'#^/images#',
								'#favicon#',
								'#\.jpg#',
								'#\.gif#',
								'#\.png#',
								'#\.css#',
								'#\.js#',
								'#\.html#'
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
	}

	public function isWhitelistRequest(){
		return $this->whitelistResource;
	}

	private function handle_plugins(){
		if(isset($this->args['plugin']) && is_array($this->args['plugin'])){
			$GWC=$this;
			foreach($this->args['plugin'] as $object => $subData){
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
		$this->siteName = preg_replace('/\:[0-9]+$/', '', $_SERVER['HTTP_HOST']);
		$this->loadProps($this->siteName);
	}

	private function loadProps($name){
		if(substr($name,0,4) == 'www.'){
			$name = substr($name,4)	;
		}
		$siteConfig = $this->root.'/sites/'.$name.'.config.php';
		if(!file_exists($siteConfig)){
			// fall back to static production file
			$siteConfig = $this->root.'/sites/production.config.php';
			if(!file_exists($siteConfig)){
				throw new Exception('Cannot find site config file here: '.$this->root.'/sites/'.$name.'.config.php');
			}
		}
		require($siteConfig);
		$this->loadConstants($CONFIG['constants'] ? $CONFIG['constants'] : array());
		$this->args = $CONFIG;
	}

	private function loadConstants($constants){
		foreach($constants as $name => $val){
			if(!defined($name)){
				define($name,$val);
			}
		}

	}

	private function setRoot(){
		$pattern = '/Windows/';
		preg_match($pattern, php_uname(), $matches);
		if(count($matches)){
			$this->root = str_replace('classes\green_web_controller.php','',__FILE__);
		} else {
			$this->root = str_replace('classes/green_web_controller.php','',__FILE__);
		}
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
		$this->LOGGER = green_logger_factory::create($this->siteName,$args);
	}

	# TODO a better way would be to use
	# override options somehow but I ain't got time for that now
	# Also need to override the logger objects already 'passed into' DB, Template, Session !!
	public function setLogger($LoggerObj){
		$this->LOGGER = $LoggerObj;
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

	public function flash_message($msg,$error=false){
		$this->SESSION->flash_message($msg,$error);
	}

	public function get_flash_messages($errors=false){
		return $this->SESSION->get_flash_messages($errors);
	}

	public function templatePut($key,$value){
		$this->TEMPLATE->addVar($key,$value);
	}

	public function render($path,$showHeaderAndFooter=TRUE,$override_header=false,$override_footer=false){
		if($override_header){
			$this->TEMPLATE->setHeaderTemplate($override_header);
		}
		if($override_footer){
			$this->TEMPLATE->setFooterTemplate($override_footer);
		}
		$this->TEMPLATE->render($path,$showHeaderAndFooter);
	}

	public function renderFooter(){
		$this->TEMPLATE->renderFooter();
	}

	public function show($path){
		$this->TEMPLATE->render($path,FALSE);
	}

	public function DBRead($sql,$params=array()){
		try {
			return $this->DATABASE->read($sql,$params);
		} catch(Exception $ex){
			$this->lastDBError = $ex->getMessage();
			$this->LOGGER->log("Cannot Read from Database: ".$this->lastDBError,$level=LOG_LEVEL_NORMAL);
			return false;
		}
	}

	public function DBWrite($sql,$params=array()){
		try{
			return $this->DATABASE->write($sql,$params);
		} catch(Exception $ex){
			$this->lastDBError = $ex->getMessage();
			$this->LOGGER->log("Cannot Write to Database: ".$this->lastDBError,$level=LOG_LEVEL_NORMAL);
			return false;
		}
	}

	public function DBLastInsertID(){
		try{
			return $this->DATABASE->lastInsertID();
		} catch(Exception $ex){
			$this->lastDBError = $ex->getMessage();
			$this->LOGGER->log("Cannot Retrieve Last Insert ID from Database: ".$this->lastDBError,$level=LOG_LEVEL_NORMAL);
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

	public function DBLastError(){
		return $this->lastDBError;
	}

	public function redirect($target){
		header('Location: '.$target);
	   	exit;
	}

	## Cleanup
	function __destruct() {
       ## Close session at this point, close log handle, close db TODO
   }
};
?>