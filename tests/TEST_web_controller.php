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
		$this->GWC->handleRequest('/data/green_software/green_framework/',$args);
		
    }
	
	public function testWeCanLogAMessageToLogFile(){
		# Test we can log
		$this->assertTrue($this->GWC->log('Test we can log '.__FILE__));
	}
	
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

	public function testWeCanRenderTemplate(){
		# Test we can render from a template
		ob_start();
		$this->GWC->render('unit_test.html');
		$contents = ob_get_clean();
		$this->assertEquals($contents,'<html><head><title>Unit Test Template</title></head><body>Unit Test Template</body></html>');
		
		# now test we can render variables
		$this->GWC->templatePut('title','GWC');
		$this->GWC->templatePut('author','Adrian Green');
		ob_start();
		$this->GWC->render('unit_test2.html');
		$contents = ob_get_clean();
		$this->assertEquals($contents,'<html><head><title>GWC Unit Test Template</title></head><body>GWC Framework by Adrian Green</body></html>'."\n");
		
	}
	
	protected function tearDown()
    {
		# close db, session, etc
		
    }

}
?>