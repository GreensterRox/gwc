<?php
require("./vendor/autoload.php");
include_once('D:\Websites\GIT_Repos\green_framework\classes\green_web_controller.php');
include_once('D:\Websites\GIT_Repos\green_framework\classes\green_logger_factory.php');

/*
**
** Test fundamental usage of the GWC framework
**
*/

class TEST_web_controller extends PHPUnit\Framework\TestCase
{

	protected function setUp(): void
    {
		 // Mock up apache run-time variables
		$_SERVER['HTTP_HOST'] = 'fake-site.co.uk';
    }

	public function testWeCanLogAMessageToLogFile(){

		$GWC = new green_web_controller();
		$GWC->handleRequest(array('logger'=>array()));

		# Test we can log
		$this->assertTrue($GWC->log('Test we can log '.__FILE__));
	}

	public function testLogLevelOfMessagesIsHonoured(){

		$dir='D:\Websites\GIT_Repos\green_framework\tests\resources\fake-site.co.uk\logs\\';

		# Test verbose level logs verbose messages
		$args=array('level' => LOG_LEVEL_VERBOSE,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test verbose level logs verbose message';
		$LOGGER->log($this_log_message,LOG_LEVEL_VERBOSE);
		$this->assertEquals($this_log_message.'^(VERBOSE)',$LOGGER->getLastMessageLogged());

		# Test normal level logs normal messages
		$args=array('level' => LOG_LEVEL_NORMAL,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test normal level logs normal message';
		$LOGGER->log($this_log_message,LOG_LEVEL_NORMAL);
		$this->assertEquals($this_log_message.'^(NORMAL)',$LOGGER->getLastMessageLogged());

		# Test normal level does NOT log verbose messages
		$args=array('level' => LOG_LEVEL_NORMAL,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test normal level does not log verbose message';
		$LOGGER->log($this_log_message,LOG_LEVEL_VERBOSE);
		$this->assertEquals('',$LOGGER->getLastMessageLogged());

		# Test none level does NOT log verbose messages
		$args=array('level' => LOG_LEVEL_NONE,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test none level does not log verbose message';
		$LOGGER->log($this_log_message,LOG_LEVEL_VERBOSE);
		$this->assertEquals('',$LOGGER->getLastMessageLogged());

		# Test none level does NOT log normal messages
		$args=array('level' => LOG_LEVEL_NONE,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test none level does not log normal message';
		$LOGGER->log($this_log_message,LOG_LEVEL_NORMAL);
		$this->assertEquals('',$LOGGER->getLastMessageLogged());


		# Test sysout level prints to standard out for verbose
		$args=array('level' => LOG_LEVEL_SYSOUT,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test sysout level prints to standard out for verbose';
		ob_start();
		$LOGGER->log($this_log_message,LOG_LEVEL_VERBOSE);
		$contents = ob_get_clean();
		$this->assertStringContainsString($this_log_message, $contents);
		$this->assertStringContainsString('SYSOUT', $contents);

		# Test sysout level prints to standard out for normal
		$args=array('level' => LOG_LEVEL_SYSOUT,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$this_log_message = 'Test sysout level prints to standard out for normal';
		ob_start();
		$LOGGER->log($this_log_message,LOG_LEVEL_NORMAL);
		$contents = ob_get_clean();
		$this->assertStringContainsString($this_log_message, $contents);
		$this->assertStringContainsString('SYSOUT', $contents);

		# Test template_out buffers messages
		$args=array('level' => LOG_LEVEL_TEMPLATE_FOOTER,'directory' => $dir);
		$LOGGER = green_logger_factory::create('fake-site.co.uk',$args);
		$log_messages = array('Test template out level buffers for normal','Test template out level buffers for verbose');
		$LOGGER->log($log_messages[0],LOG_LEVEL_NORMAL);
		$LOGGER->log($log_messages[1],LOG_LEVEL_VERBOSE);
		foreach($log_messages as $key => $msg){
			$this->assertStringContainsString($msg,$LOGGER->getMessageBuffer()[$key]);
		}



	}


	public function testWeCanWriteToSession(){

		$GWC = new green_web_controller();
		$GWC->handleRequest(array('session'=>true));

		# Test we can initiate a session
		$this->assertMatchesRegularExpression('/\d+/', $GWC->sessionId());

		# Test values can be put and retrieved
		$key = 'myValue';
		$value = 'Green_Framework_Is_Cool';
		$GWC->sessionPut($key,$value);
		$retrievedValue = $GWC->sessionGet($key);
		$this->assertEquals($value,$retrievedValue);
	}


	public function testWeCanRenderTemplate(){

		$GWC = new green_web_controller();
		$GWC->handleRequest(array('template'=>true,'logger'=>array('level'=>LOG_LEVEL_NONE)));

		# Test we can render from a template and render variables
		$GWC->templatePut('title','GWC');
		$GWC->templatePut('author','Adrian Green');
		ob_start();
		$GWC->render('unit_test.html');
		$contents = str_replace(array("\r", "\n"), '', ob_get_clean());
		$this->assertEquals('<html><head><title>GWC Unit Test Template</title></head><body>GWC Framework by Adrian Green</body><footer>This is the footer</footer></html>',$contents);


		# Test we can render template with log messages automatically appended

	}

	public function testWeCanRenderTemplateWithAppendedLogMessages(){

		$GWC = new green_web_controller();
		$GWC->handleRequest(array('template'=>true,'logger'=>array('level'=>LOG_LEVEL_TEMPLATE_FOOTER)));

		# Test we can render from a template and render variables
		$GWC->templatePut('title','GWC');
		$GWC->templatePut('author','Adrian Green');
		ob_start();
		$GWC->render('unit_test.html');
		$contents = str_replace(array("\r", "\n"), '', ob_get_clean());
		$this->assertStringContainsString('class="logmsg"',$contents);

	}

	// TO DO - tests below here need a functioning database
/*	public function testWeCanReadAndWriteDatabase(){

		$GWC = new green_web_controller($debug=true);
		$GWC->handleRequest(array('database'=>true));

		$rs = $GWC->DBWrite('CREATE TABLE IF NOT EXISTS user_test (id int PRIMARY KEY auto_increment,name varchar(64) NOT NULL,value varchar(64) NOT NULL,last_updated datetime NOT NULL) CHARACTER SET utf8;');
		$this->assertEquals($rs,true);

		$rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES (:name, :value, NOW())',array(':name'=>'Green Framework Author',':value'=>"Adrian Green"));
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

		$rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES (:name, :value, NOW())',array(':name'=>'Green Framework Author',':value'=>"Adrian Green"));
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

		$rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES (:name, :value, NOW())',array(':name'=>'Green Framework Author',':value'=>"Adrian Green"));
		$this->assertEquals($rs,true);

		$GWC->DBRollback();

		$rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Adrian Green'));
		$this->assertEmpty($rs);

		$rs = $GWC->DBWrite('DROP TABLE IF EXISTS user_test');
		$this->assertTrue($rs);

	}*/

	protected function tearDown(): void
    {
		# close db, session, etc

    }

}
?>