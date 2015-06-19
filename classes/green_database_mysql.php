<?php

Class green_database_mysql {
	
	private $logger;
	private $conn;
	
	function __construct($logger) {
		$this->logger = $logger;
   	}
	
	// See: https://github.com/adriengibrat/Simple-Database-PHP-Class/blob/master/Db.php
	// http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
	public function connect($dbServer,$dbName,$dbUsername,$dbPassword){
		$this->logger->log('DATABASE: Connecting to ['.$dbUsername.'@'.$dbServer.'/'.$dbName.'] ',LOG_LEVEL_VERBOSE);
		
		try {
			$this->conn = new pdo('mysql:host='.$dbServer.';dbname='.$dbName, $dbUsername, $dbPassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) );
			$this->conn->exec('SET NAMES UTF8');
		} catch (PDOException $ex){
			$this->logger->log("DATABASE: Connection failed: " . $ex->getMessage(),LOG_LEVEL_NORMAL);
		    $this->fail('Unable to connect to the database - please check the settings');
		}
	}
	
	public function lastError(){
		
	}
	
	public function read ( $sql, array $params ) {
		$this->logger->log("DATABASE (read): " . $this->renderWithParams($sql,$params),LOG_LEVEL_VERBOSE);
		$stmt = $this->conn->prepare($sql);
		if (!$stmt) {
		    $this->logger->log("DATABASE: Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
		    $rows = false;
		} else {
			$stmt->execute( $params );
			$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return $rows;
	}
	
	# TO DO shopuldn't this use prepared statements like READ above ??
	public function write($sql){
		$this->logger->log("DATABASE (write): " . $sql,LOG_LEVEL_VERBOSE);
		$stmt = $this->conn->query( $sql );
		if (!$stmt) {
		    $this->logger->log("DATABASE: Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
		    $result = false;
		} else {
			$result = true;
		}
		return $result;
	}
	
	private function renderWithParams($sql,$params){
		# TO DO replace params into sql so we can see what is actually being run
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