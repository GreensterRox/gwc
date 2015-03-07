<?php

define ('LOG_LEVEL_VERBOSE',1);
define ('LOG_LEVEL_NORMAL',2);
define ('LOG_LEVEL_NONE',3);	# why would you ever need this ??
define ('LOG_LEVEL_OUT',99);

Class green_logger {
	
	private $path;
	private $sep;
	private $level = LOG_LEVEL_NORMAL;
	
	function __construct($name,$sep='^') {
		$this->sep=$sep;
		$this->buildPath($name);
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
			// use debug backtrace to get name of calling method TO DO
		}
		
		if($this->level == LOG_LEVEL_OUT){
			print $this->formatMessage($msg);
		} elseif($level >= $this->level && $this->level != LOG_LEVEL_NONE){
			error_log ($this->formatMessage($msg),3,$this->path);
		}
	}
	
	private function formatMessage($msg){
		return date('H:i:s d/m/Y').$this->sep.$msg."\n";
	}
	
};

?>