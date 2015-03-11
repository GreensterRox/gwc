<?php
include_once('/data/green_software/green_framework/classes/green_web_controller.php');


class WebControllerTest extends PHPUnit_Framework_TestCase
{
	
	protected function setUp()
    {
		 // Mock up apache run-time variables
		$_SERVER['HTTP_HOST'] = 'fake-site.co.uk';
    }
	
	public function testWeCanLogAMessageToLogFile(){
		
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('logger'=>true));
		
		# Test we can log
		$this->assertTrue($GWC->log('Test we can log '.__FILE__));
	}
	
	public function testWeCanWriteToSession(){
		
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('session'=>true));
		
		# Test we can initiate a session
		$this->assertRegExp('/\d+/', $GWC->sessionId());
		
		# Test values can be put and retrieved
		$key = 'myValue';
		$value = 'Green_Framework_Is_Cool';
		$GWC->sessionPut($key,$value);
		$retrievedValue = $GWC->sessionGet($key);
		$this->assertEquals($value,$retrievedValue);
	}

	public function testWeCanRenderTemplate(){
		
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('template'=>true));
		
		# Test we can render from a template
		ob_start();
		$GWC->render('unit_test.html');
		$contents = ob_get_clean();
		$this->assertEquals($contents,'<html><head><title>Unit Test Template</title></head><body>Unit Test Template</body></html>');
		
		# now test we can render variables
		$GWC->templatePut('title','GWC');
		$GWC->templatePut('author','Adrian Green');
		ob_start();
		$GWC->render('unit_test2.html');
		$contents = ob_get_clean();
		$this->assertEquals($contents,'<html><head><title>GWC Unit Test Template</title></head><body>GWC Framework by Adrian Green</body></html>'."\n");
		
	}
	
	public function testWeCanConnectToDatabase(){
		
		# TO DO - test should create table and destroy it again
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('database'=>true));
		
		# test database commands
		$rs = $GWC->rawQuery('CREATE TABLE IF NOT EXISTS user_test (id int PRIMARY KEY auto_increment,name varchar(64) NOT NULL,value varchar(64) NOT NULL,last_updated datetime NOT NULL) CHARACTER SET utf8;');
		
		die(var_dump($rs));
		
		$rs = $GWC->rawQuery('DROP TABLE IF EXISTS user_test');
		
	}
	
	protected function tearDown()
    {
		# close db, session, etc
		
    }

}
?>