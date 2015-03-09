<?php

Class green_database_mysql {
	
	private $logger;
	private $name;
	
	function __construct($name,$logger) {
		$this->logger = $logger;
		
		$this-connect();
   	}
	
	private function connect(){
		$this->logger->log('DATABASE: Connecting to [??]',LOG_LEVEL_VERBOSE);
		
		# TO DO implement this
		$servername = "localhost";
		$username = "username";
		$password = "password";
		$dbname = "myDB";
		
		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
		    die("Connection failed: " . $conn->connect_error);
		}
		
		// sql to create table
		$sql = "CREATE TABLE MyGuests (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		firstname VARCHAR(30) NOT NULL,
		lastname VARCHAR(30) NOT NULL,
		email VARCHAR(50),
		reg_date TIMESTAMP
		)";
		
		if ($conn->query($sql) === TRUE) {
		    echo "Table MyGuests created successfully";
		} else {
		    echo "Error creating table: " . $conn->error;
		}
		
		$conn->close();
	}
	
}

?>