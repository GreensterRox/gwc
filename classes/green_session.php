<?php

Class green_session {
	
	private $name;
	private $logger;
	private $sessionId;
	
	function __construct($name,$logger) {
		$this->logger = $logger;
		$this->name = trim($name);
   	}
   	
   	public function start(){
   		session_start();
   		$this->sessionId = session_id();
   	}
   	
   	public function getSessionId(){
   		return $this->sessionId;
   	}
   	
   	public function put($key,$value){
   		if(is_object($value)){
   			$this->logger->log('SESSION: Putting key ['.serialize($key).'] with value [Object]',LOG_LEVEL_VERBOSE);
   		} else {
   			$this->logger->log('SESSION: Putting key ['.serialize($key).'] with value ['.serialize($value).']',LOG_LEVEL_VERBOSE);
   		}
   		$_SESSION[$this->name][$key] = $value;
   	}
   	
   	public function get($key){
   		$this->logger->log('SESSION: Getting key ['.$key.'] ',LOG_LEVEL_VERBOSE);
   		if(isset($_SESSION[$this->name][$key])){
   			return $_SESSION[$this->name][$key];
   		} else {
   			return false;
   		}
   	}
   	
   	public function flash_message($msg){
		if(!empty($msg)){
			if(!isset($_SESSION[$this->name]['FLASH_MESSAGES'])){
				$_SESSION[$this->name]['FLASH_MESSAGES'] = array();
			}
			$_SESSION[$this->name]['FLASH_MESSAGES'][] = $msg;
		}
		return true;
	}
	
	public function get_flash_messages(){
		$messages = array();
		if(isset($_SESSION[$this->name]['FLASH_MESSAGES'])){
			$messages = $_SESSION[$this->name]['FLASH_MESSAGES'];
		}
		unset ($_SESSION[$this->name]['FLASH_MESSAGES']);
		return $messages;
	}
   	
   	# Yes I know not correct way to mock/stub - I needed a quick way to fool phpunit and not have to deal with buffering crap - sue me
   	public function mock(){
   		$this->logger->log('SESSION: Mock session ',LOG_LEVEL_VERBOSE);
   		$this->sessionId = '1234';
   	}
   	
   	private function fail($msg){
   		$this->logger->log('SESSION: '.$msg);
   		throw new Exception($msg);
   	}
   	
}
?>