<?php

Class green_logger {
	
	private $path;
	private $sep;
	private $level = LOG_LEVEL_NORMAL;
	
	function __construct($name,$logLevel,$sep='^') {
		$this->sep=$sep;
		$this->buildPath($name);
		$this->level=$logLevel;
   	}
	
	private function buildPath($name){
		if(empty($name)){
			throw new Exception('Framework Failure - missing required data (Green Logger expects a log file name)');
		}
		$this->path = '/var/log/green_framework/'.$name.'.log';
	}
	
	public function log($msg,$level=LOG_LEVEL_NORMAL){
		if($level > 4 || $level < 1){
			$level = LOG_LEVEL_NORMAL;
		}
		
		if(empty($msg)){
			// use debug backtrace to get name of calling method TODO
		}
		
		if($this->level == LOG_LEVEL_OUT){
			print $this->formatMessage($msg);
		} elseif($level >= $this->level && $this->level != LOG_LEVEL_NONE){
			return error_log ($this->formatMessage($msg),3,$this->path);
		}
	}
	
	private function formatMessage($msg){
		# TODO Use session to log the user id (and name) of the user that performed this action
		return date('H:i:s d/m/Y').$this->sep.$msg."\n";
	}
	
};

?>