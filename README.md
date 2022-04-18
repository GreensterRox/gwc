# The Green Web Framework is an Ultra Lightweight PHP Web Framework for rapid development of web sites !
## Intended audience: Tech professionals who are already proficient with PHP & Apache.
## Note: Currently only supports MySQL & MariaDB

### Follow the installation instructions below and you'll get a variable called $GWC available in all your PHP scripts. This variable allows you to read and write to a database, manage session variables, render templates and write logs.
The GWC framework will also allow you to map URL routes to specific classes giving you massive flexibility when writing websites and enhancing your delivery time.

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

4.) Put the GWC include path in you PHP ini
> sed -i '$ a include_path = ".:/var/www/gwc/classes:/var/www/gwc/tests"' /etc/php5/apache2/php.ini

5.) NOTE. You do not need to pull the GWC files into your own web application folder. The GWC should live in a separate directory to your website. Once you've done steps 1-4 return to your index.php page and start using the magic $GWC variable.

### Unit tests with PhpUnit

1.) Install composer

https://getcomposer.org/download/

```
/c/php/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
/c/php/php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
/c/php/php composer-setup.php
/c/php/php -r "unlink('composer-setup.php');"
```

2.) Install phpunit

``` /c/php/php composer.phar require phpunit/phpunit ```

3.) Run tests (requires db connection and pre-installed schema) TO DO add instructions for creating db

``` /c/php/php -f ./vendor/bin/phpunit tests/TEST_web_controller.php ```