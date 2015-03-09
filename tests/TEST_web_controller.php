<?php
include_once('/data/green_software/green_framework/classes/green_web_controller.php');


class WebControllerTest extends PHPUnit_Framework_TestCase
{
	
	protected function setUp()
    {
		 // Mock up apache run-time variables
		$_SERVER['HTTP_HOST'] = 'fake-site.co.uk';
		$this->GWC = new green_web_controller($debug=true);
		// run-time args
		$args['session']['session_mock'] = true;		// don't actually start a session
		$this->GWC->handleRequest($args);
		
    }
	
	public function testWeCanLogAMessageToLogFile(){
		# Test we can log
		$this->assertTrue($this->GWC->log('Test we can log '.__FILE__));
	}
	
	
	## Doesn't work because out already started by phpunit
	## TO DO figure a way round this
	public function testWeCanWriteToSession(){

		# Test we can initiate a session
		$this->assertRegExp('/\d+/', $this->GWC->sessionId());
		
		# Test values can be put and retrieved
		$key = 'myValue';
		$value = 'Green_Framework_Is_Cool';
		$this->GWC->sessionPut($key,$value);
		$retrievedValue = $this->GWC->sessionGet($key);
		$this->assertEquals($value,$retrievedValue);
	}

	protected function tearDown()
    {
		# close db, session, etc
		
    }

}
?>