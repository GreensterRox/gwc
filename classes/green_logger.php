<?php

Class green_logger {
	
	private $path;
	private $sep;
	private $level = LOG_LEVEL_NORMAL;
	private $lastMessageLogged;
	private $messageBuffer=array();
	
	function __construct($name,$args,$sep='^') {
		$this->sep=$sep;
		$this->buildPath($args,$name);
		if(isset($args['level'])){
			$this->level=(int)$args['level'];	
		}
   	}
	
	private function buildPath($args,$name){
		if(empty($name)){
			throw new Exception('Framework Failure - missing required data (Green Logger expects a log file name & log directory)');
		}
		$logDir = '/var/log/green_framework/';
		if(isset($args['directory'])){
			$logDir = $args['directory'];
		}
		if(isset($args['name'])){
			$name = $args['name'];
		}
		$this->path = $logDir.$name.'.log';
	}
	
	public function log($msg,$levelOfMessage=LOG_LEVEL_NORMAL){
		if($levelOfMessage > 5 || $levelOfMessage < 1){
			$levelOfMessage = LOG_LEVEL_NORMAL;
		}
		
		if(empty($msg)){
			// use debug backtrace to get name of calling method TODO
		}
		
		$suffix='';
		switch($this->level){
			case LOG_LEVEL_SYSOUT:
				print "\n".">>>>>".$this->formatMessage($msg.$this->sep.'(SYSOUT)');
				break;
			case LOG_LEVEL_NONE:
				// do nothing
				break;
			case LOG_LEVEL_VERBOSE:
				$suffix = $this->sep.'(VERBOSE)';
			case LOG_LEVEL_NORMAL:
				if(empty($suffix)){
					$suffix = $this->sep.'(NORMAL)';
				}
				if($levelOfMessage >= $this->level){
					$this->lastMessageLogged=$msg.$suffix;
					return error_log ($this->formatMessage($msg.$suffix),3,$this->path);
				}
				break;
			case LOG_LEVEL_TEMPLATE_FOOTER:
				$this->messageBuffer[] = $this->formatMessage($msg.$suffix);
				return TRUE;
		}
		
		return true;
	}
	
	private function formatMessage($msg){
		# TODO Use session to log the user id (and name) of the user that performed this action
		return date('H:i:s d/m/Y').$this->sep.$msg."\n";
	}
	
	public function getLastMessageLogged(){
		return $this->lastMessageLogged;
	}
	
	public function getMessageBuffer(){
		return $this->messageBuffer;
	}
};

?>