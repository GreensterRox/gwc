<?php
	
	# full path to template directory
	$CONFIG['template']['directory'] = '/data/green_software/green_framework/tests/resources/fake-site.co.uk/templates/';
		
	# Optionally set a header and footer which will be automatically rendered with every page
	$CONFIG['template']['template_header'] = '/path/to/header.html';
	$CONFIG['template']['template_footer'] = '/path/to/footer.html';
	# $GWC->render('template');
	# For No header or Footer do: $GWC->render('template',FALSE)
	# To override header and footer do: $GWC->render('template',TRUE,'/path/to/header.html','/path/to/footer')
	
	# DB Creds
	$CONFIG['database']['server'] = 'localhost';
	$CONFIG['database']['name'] = 'gwc_test';
	$CONFIG['database']['username'] = 'test_user';
	$CONFIG['database']['password'] = '0nlyth3br4v3gwc';
	
	# Run-time options
	$CONFIG['options']['debug'] = true;
	$CONFIG['session']['session_mock'] = true;
	
	# Enable friendly URL routes
	# TODO put example routes config in this repo
	$CONFIG['routing']['on'] = TRUE;
	$CONFIG['routing']['routes_file'] = '/path/to/routes.config';	
													
	#####################################
	## Constants available to entire application
	## can be defined here
	#####################################
	define('__SUPPORT__EMAIL__','2015@voteworld.co.uk');
	
	#####################################
	## Other docs
	#####################################
	# Flash messages - appear only once (including after redirect)
	#$GWC->flash_message('You have Successfully Updated Your Details');
	# To diffentiate flash_errors, do
	#GWC->flash_message('You Failed To Provide Enough Information',TRUE);
	# To display Flash Messages / Flash Errors here is example HTML:
	# To get Flash Messages do: $flash_messages = $GWC->get_flash_messages();
	# To get Flash Errors do: $flash_messages = $GWC->get_flash_messages(TRUE);
	
	# Enable Automatic CSRF Protection
	# Forces each and every form on the site to pass back the GWC->CSRF_TOKEN
	# in a hidden form field called 'gwc_csrf'
	# Simply put <?=$GWC->CSRF_protection()?> inside your FORM tags
	$CONFIG['options']['CSRF_protection'] = TRUE;
	
?>