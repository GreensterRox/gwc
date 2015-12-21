#The Green Web Framework is an Ultra Lightweight PHP Web Framework for rapid development of web sites !
##Intended audience: Tech professionals who are already proficient with PHP & Apache.

### No need to understand database abstraction layers like ORM, just do this:
  > $rs = $GWC->DBRead('SELECT * FROM user_test WHERE value = :value',array(':value' => 'Mr. Grinch'));
  
  > $rs = $GWC->DBWrite('INSERT INTO user_test (name,value,last_updated) VALUES (:name, :value, NOW())',array(':name'=>'Green Framework Author',':value'=>"GreensterRox"));
  
### No need to handle session management, just do this:
  > $key = 'myValue';
  
> $value = 'Green_Framework_Is_Cool';
	 
> $GWC->sessionPut($key,$value);
	 
> $retrievedValue = $GWC->sessionGet($key);
	
### No need for a template engine, just add your HTML files to the template directory:
> $GWC->templatePut('title','GWC');
  
> $GWC->templatePut('author','GreensterRox');
	 
> $GWC->render('unit_test2.html');  // (make sure this file is in your include path)

Access yor variables with <?=$author?> and <?=$title?> inside your unit_test2.html template.
(Or <?php print $author ?> if not using short tags)
	 
### No need to implement a logger, just do:
	 
> $GWC->log('This is a log message');

By default logs get written to /var/log/green_framework/

### Installation Instructions

1.) Download GWC framework and put somewhere on your web server.
> mkdir /var/www/gwc

> cd /var/www/gwc

> git clone git@github.com:GreensterRox/gwc.git

2.) Add this line to your apache configuration file (or vhost), this makes the $GWC variable available to all your scripts:
> php_value auto_prepend_file "/var/www/gwc/controllers/web_controller.php"

3.) Copy the sample configuration file into a file with the same name as your DNS host and replace the values. 
> cp /var/www/gwc/sites/fake-site.co.uk.config.php /var/www/gwc/sites/${yourhost.com}.config.php
