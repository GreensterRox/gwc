<?php

Class green_database_mysql {
	
	private $logger;
	private $conn;
	
	function __construct($logger) {
		$this->logger = $logger;
   	}
	
	public function connect($dbServer,$dbName,$dbUsername,$dbPassword){
		$this->logger->log('DATABASE: Connecting to ['.$dbUsername.'@'.$dbServer.'/'.$dbName.'] ',LOG_LEVEL_VERBOSE);
		
		// Create connection
		$conn = new mysqli($dbServer, $dbUsername, $dbPassword, $dbName);
		// Check connection
		if (!$conn) {
		    $this->logger->log("DATABASE: Connection failed: " . $conn->connect_error,LOG_LEVEL_NORMAL);
		    $this->fail('Unable to connect to the database - please check the settings');
		}
		
		$this->conn = $conn;
	}
	
	public function query($sql,$vars){
		$this->logger->log("DATABASE: " . $this->renderWithVars($sql,$vars),LOG_LEVEL_VERBOSE);
		$success = $this->conn->query($sql);
		if (!$success) {
		    $this->logger->log("DATABASE: Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
		}
		return $success;
	}
	
	# queries and returns rows in associated array
	# TO DO handle prepared statements
	public function fetch($sql,$vars){
		$result = $this->query($sql,$vars);
		$rowSet = array();
		
		if($result){
			if()
			while ($row = $result->fetch_assoc()) {
			    echo " id = " . $row['id'] . "\n";
			}
		}
	}
	
	private function fail($msg){
   		$this->logger->log('DATABASE: '.$msg);
   		throw new Exception($msg);
   	}
	
	function __destruct() {
		if($this->conn){
       		$this->conn->close();
    	}
   	}
}

?>