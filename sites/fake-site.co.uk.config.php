<?php
	
	# full path to template directory
	$CONFIG['template']['directory'] = 'D:\Websites\GIT_Repos\green_framework\tests\resources\fake-site.co.uk\templates\\';
		
	# Optionally set a header and footer which will be automatically rendered with every page
	$CONFIG['template']['template_header'] = 'D:\Websites\GIT_Repos\green_framework\tests\resources\fake-site.co.uk\templates\header.html';
	$CONFIG['template']['template_footer'] = 'D:\Websites\GIT_Repos\green_framework\tests\resources\fake-site.co.uk\templates\footer.html';
	# $GWC->render('template');
	# For No header or Footer do: $GWC->render('template',FALSE)
	# To override header and footer do: $GWC->render('template',TRUE,'/path/to/header.html','/path/to/footer')
	
	# DB Creds
	$CONFIG['database']['server'] = 'localhost';
	$CONFIG['database']['name'] = 'gwc_test';
	$CONFIG['database']['username'] = 'test_user';
	$CONFIG['database']['password'] = '0nlyth3br4v3gwc';
	
	#########################
	# Logging
	#
	# 'level' - Integer / Valid Options:
	#			1 = VERBOSE = log everything / including every database query / make sure to rotate your log files as these will get big
	#			2 = NORMAL = log normally / important events are logged as well as errors
	#			3 = NONE = log nothing
	#			4 = TEMPLATE_FOOTER = log everything as VERBOSE but instead of logging to a file it will render to template_footer
	#			5 = VERBOSE_SYSOUT = log everything as VERBOSE but instead of logging to a file it will send to standard out (console)
	##########################
	$CONFIG['logger']['directory'] = 'D:\Websites\GIT_Repos\green_framework\tests\resources\fake-site.co.uk\logs\\';
	$CONFIG['logger']['level'] = 5;
	
	# Run-time options
	$CONFIG['session']['session_mock'] = true;
	
	# Enable friendly URL routes
	# TODO put example routes config in this repo
	$CONFIG['routing']['on'] = FALSE;
	$CONFIG['routing']['routes_file'] = '/path/to/routes.config';
													
	#####################################
	## Constants available to entire application
	## can be defined here
	#####################################
	$CONFIG['constants']['__SUPPORT_EMAIL__'] = '2022@voteworld.co.uk';
	
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
	
	/*
	# Enable Automatic CSRF Protection
	# Forces each and every form on the site to pass back the GWC->CSRF_TOKEN
	# in a hidden form field called 'gwc_csrf'
	# Simply put <?=$GWC->CSRF_protection()?> inside your FORM tags
	# $CONFIG['options']['CSRF_protection'] = TRUE;
	*/
?>