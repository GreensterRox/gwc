<?php

Class green_database_mysql {

	private $logger;
	private $conn;
	private $server;
	private $database_name;
	private $username;
	private $password;

	function __construct($logger,$args) {
		$this->logger = $logger;
		$this->server = $args['server'];
		$this->database_name = $args['name'];
		$this->username = $args['username'];
		$this->password = $args['password'];
   	}

	// See: https://github.com/adriengibrat/Simple-Database-PHP-Class/blob/master/Db.php
	// http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
	private function connect(){
		if(empty($this->conn)){
			$this->logger->log('DATABASE: Connecting to ['.$this->username.'@'.$this->server.'/'.$this->database_name.'] ',LOG_LEVEL_VERBOSE);
			try {
				$this->conn = new pdo('mysql:host='.$this->server.';dbname='.$this->database_name.';charset=utf8mb4', $this->username, $this->password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4") );
			} catch (PDOException $ex){
				$this->logger->log("DATABASE: Connection failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
			    $this->fail('Unable to connect to the database - please check the settings');
			}
		} else {
			$this->logger->log('DATABASE: Connection already established ',LOG_LEVEL_VERBOSE);
		}


	}

	public function lastError(){
		// not needed, handled by exception catcher in web_controller
	}

	// TODO Susceptable to SQL Injection through dynamic ORDER BY (and other suffix clauses) - anything dynamic MUST be passed through this class?? Figure out how to fix this. Needs testing to validate this assumption
	public function read ( $sql, array $params ) {
		$timer_start = microtime(true);
		$rended_query = "DATABASE (read): " . $this->renderWithParams($sql,$params);
		$this->connect();
		$stmt = $this->conn->prepare($sql);
		$success=false;
		if (!$stmt) {
		    $this->logger->log("Prepare Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
		    $rows = false;
		} else {
			try{
			$stmt->execute( $params );
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
				$success=true;
			} catch(Exception $ex){
				$this->logger->log("DATABASE (read): Failed - Exception: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
			}
		}
		$timer_stop = microtime(true);
		if($success){
			$timing = number_format(($timer_stop-$timer_start),5)." secs";
		} else {
			$timing = '<b><font color="red">FAIL</font></b>';
		}
		$log_message = $rended_query."|".$timing;
		$this->logger->log($log_message,LOG_LEVEL_VERBOSE);
		return $rows;
	}

	// TODO Susceptable to SQL Injection through dynamic ORDER BY (and other suffix clauses) - anything dynamic MUST be passed through this class?? Figure out how to fix this. Needs testing to validate this assumption
	public function write ( $sql, array $params ) {
		$timer_start = microtime(true);
		$this->connect();
		$stmt = $this->conn->prepare($sql);
		$rended_query = "DATABASE (write): " . $this->renderWithParams($sql,$params);
		$result = false;
		if (!$stmt) {
		    $this->logger->log("Prepare Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
		} else {
			try {
			$result = $stmt->execute( $params );
				if(!$result){
				$this->logger->log("DATABASE (write): Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
			}
			} catch(Exception $ex){
				$this->logger->log("DATABASE (write): Failed - Exception: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
			}
		}
		$timer_stop = microtime(true);
		if($result){
			$timing = number_format(($timer_stop-$timer_start),5)." secs";
		} else {
			$timing = '<b><font color="red">FAIL</font></b>';
		}
		$log_message = $rended_query."|".$timing;
		$this->logger->log($log_message,LOG_LEVEL_VERBOSE);
		return $result;
	}

	public function lastInsertID() {
		$this->connect();
		return $this->conn->lastInsertId();
	}

	private function renderWithParams($sql,$params){
		foreach($params as $k=>$v){
			# could prob make this better but this'll do for now
			if(!is_numeric($v)){
				$sql = str_replace($k,'"'.$v.'"',$sql);
			} else {
				$sql = str_replace($k,$v,$sql);
			}
		}
		return $sql;
	}

	public function startTransaction(){
		$this->connect();
		$this->logger->log("DATABASE (startTransaction): ",LOG_LEVEL_VERBOSE);
		try {
			$res = $this->conn->beginTransaction();
			if(!$res){
				$this->logger->log("DATABASE: Begin Transaction failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    	$this->fail('Unable to begin transaction');
			}
		} catch(PDOException $ex){
			$this->logger->log("DATABASE: Begin Transaction failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    $this->fail('Unable to begin transaction');
		}
	}

	public function commit(){
		$this->logger->log("DATABASE (commit): ",LOG_LEVEL_VERBOSE);
		try {
			$res = $this->conn->commit();
			if(!$res){
				$this->logger->log("DATABASE: Commit failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    	$this->fail('Unable to begin transaction');
			}
		} catch(PDOException $ex){
			$this->logger->log("DATABASE: Commit failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    $this->fail('Unable to begin transaction');
		}
	}

	public function rollback(){
		$this->logger->log("DATABASE (rollback): ",LOG_LEVEL_VERBOSE);
		try {
			$res = $this->conn->rollback();
			if(!$res){
				$this->logger->log("DATABASE: Rollback failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    	$this->fail('Unable to begin transaction');
			}
		} catch(PDOException $ex){
			$this->logger->log("DATABASE: Rollback failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    $this->fail('Unable to begin transaction');
		}
	}

	private function fail($msg){
   		$this->logger->log('DATABASE: '.$msg);
   		throw new Exception($msg);
   	}

	function __destruct() {
		if($this->conn){
       		$this->conn = null;
    	}
   	}
}

?>