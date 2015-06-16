<?php
include_once('/data/green_software/green_framework/classes/green_web_controller.php');

/*
**
** Test fundamental usage of the GWC framework
**
*/

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
	
	public function testWeCanReadAndWriteDatabase(){
		
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('database'=>true));
		
		$rs = $GWC->DBWrite('CREATE TABLE IF NOT EXISTS user_test (id int PRIMARY KEY auto_increment,name varchar(64) NOT NULL,value varchar(64) NOT NULL,last_updated datetime NOT NULL) CHARACTER SET utf8;');
		$this->assertEquals($rs,true);
		
		$rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES ("Green Framework Author", "Adrian Green", NOW())');
		$this->assertEquals($rs,true);
		
		$rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Adrian Green'));
		$this->assertEquals($rs[0]['value'],"Adrian Green");
		
		# negative test
		$rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Mr. Grinch'));
		$this->assertEmpty($rs);
		
		$rs = $GWC->DBWrite('DROP TABLE IF EXISTS user_test');
		$this->assertEquals($rs,true);
		
		
		
	}

	public function testDatabaseTransactions(){
		
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('database'=>true));
		
		// Make sure this doesn't exist before doing any further tests
		$rs = $GWC->DBWrite('DROP TABLE IF EXISTS user_test');
		$this->assertEquals($rs,true);
		
		$rs = $GWC->DBWrite('CREATE TABLE IF NOT EXISTS user_test (id int PRIMARY KEY auto_increment,name varchar(64) NOT NULL,value varchar(64) NOT NULL,last_updated datetime NOT NULL) CHARACTER SET utf8;');
		$this->assertEquals($rs,true);
		
		$GWC->DBStartTransaction();
		
		$rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES ("Green Framework Author", "Adrian Green", NOW())');
		$this->assertEquals($rs,true);
		
		$GWC->DBCommit();
		
		$rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Adrian Green'));
		$this->assertEquals($rs[0]['value'],"Adrian Green");
		
		# negative test
		$rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Mr. Grinch'));
		$this->assertEmpty($rs);
		
		$rs = $GWC->DBWrite('DROP TABLE IF EXISTS user_test');
		$this->assertEquals($rs,true);	
		
	}

	public function testDatabaseRollback(){
		
		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('database'=>true));
		
		// Make sure this doesn't exist before doing any further tests
		$rs = $GWC->DBWrite('DROP TABLE IF EXISTS user_test');
		$this->assertEquals($rs,true);
		
		$rs = $GWC->DBWrite('CREATE TABLE IF NOT EXISTS user_test (id int PRIMARY KEY auto_increment,name varchar(64) NOT NULL,value varchar(64) NOT NULL,last_updated datetime NOT NULL) CHARACTER SET utf8;');
		$this->assertEquals($rs,true);
		
		$GWC->DBStartTransaction();
			
		$rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES ("Green Framework Author", "Adrian Green", NOW())');
		$this->assertEquals($rs,true);
		
		$GWC->DBRollback();
		
		$rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Adrian Green'));
		$this->assertEmpty($rs);
		
		$rs = $GWC->DBWrite('DROP TABLE IF EXISTS user_test');
		$this->assertTrue($rs);	
		
	}

	protected function tearDown()
    {
		# close db, session, etc
		
    }

}
?>