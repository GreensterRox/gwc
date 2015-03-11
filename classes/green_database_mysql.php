<?php

Class green_database_mysql {
	
	private $logger;
	private $conn;
	private $result;
	private $statement = array();
	
	function __construct($logger) {
		$this->logger = $logger;
   	}
	
	// See: https://github.com/adriengibrat/Simple-Database-PHP-Class/blob/master/Db.php
	public function connect($dbServer,$dbName,$dbUsername,$dbPassword){
		$this->logger->log('DATABASE: Connecting to ['.$dbUsername.'@'.$dbServer.'/'.$dbName.'] ',LOG_LEVEL_VERBOSE);
		
		$this->conn = new pdo('mysql:host='.$dbServer.';dbname='.$dbName, $dbUsername, $dbPassword, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION) );
		$this->conn->exec('SET NAMES UTF8');
		
		// Check connection
		if (!$this->conn) {
		    $this->logger->log("DATABASE: Connection failed: " . $this->errorInfo(),LOG_LEVEL_NORMAL);
		    $this->fail('Unable to connect to the database - please check the settings');
		}
	}
	
	public function query ( $sql, array $params ) {
		$this->logger->log("DATABASE: " . $this->renderWithParams($sql,$params),LOG_LEVEL_VERBOSE);
		$this->result = isset( $this->statement[$sql] ) ?
		$this->statement[$sql] :
		$this->statement[$sql] = $this->conn->prepare($sql);
		$this->result->execute( $params );
		print 'TO DO - make this an assoc array to return';
		die(var_dump($this));
	}
	
	public function rawQuery($sql){
		$this->logger->log("DATABASE: " . $sql,LOG_LEVEL_VERBOSE);
		$this->result = $this->conn->query( $sql );
		if (!$this->result) {
		    $this->logger->log("DATABASE: Query failed: " . $this->conn->error,LOG_LEVEL_NORMAL);
		}
		print 'TO DO - make this an assoc array to return';
		die(var_dump($this->result));
	}
	
	private function renderWithParams($sql,$params){
		# TO DO replace params into sql so we can see what is actually being run
	}
	
	# queries and returns rows in associated array
	# TO DO handle prepared statements
	public function xxxquery($sql,$vars){
		$result = $this->query($sql,$vars);
		$rowSet = array();
		
		if($result){
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
       		$this->conn = null;
    	}
   	}
}

?>