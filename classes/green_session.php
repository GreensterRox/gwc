<?php

Class green_session {
	
	private $name;
	private $logger;
	private $sessionId;
	
	function __construct($name,$logger) {
		$this->logger = $logger;
   	}
   	
   	public function start(){
   		session_start();
   		$this->sessionId = session_id();
   	}
   	
   	public function getSessionId(){
   		return $this->sessionId;
   	}
   	
   	public function put($key,$value){
   		$this->logger->log('SESSION: Putting key ['.$key.'] | value ['.$value.']',LOG_LEVEL_VERBOSE);
   		$_SESSION[$this->name][$key] = $value;
   	}
   	
   	public function get($key){
   		$this->logger->log('SESSION: Getting key ['.$key.'] ',LOG_LEVEL_VERBOSE);
   		return $_SESSION[$this->name][$key];
   	}
   	
   	# Yes I know not correct way to mock/stub - I needed a quick way to fool phpunit and not have to deal with buffering crap - sue me
   	public function mock(){
   		$this->logger->log('SESSION: Mock session ',LOG_LEVEL_VERBOSE);
   		$this->sessionId = '1234';
   	}
   	
}
?>