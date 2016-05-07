<?php
	
	# Full path to template directory
	$CONFIG['template']['directory'] = '/var/www/gwc-skeleton/templates/';
	
	# DB Creds
	$CONFIG['database']['server'] = 'localhost';
	$CONFIG['database']['name'] = 'db_name';
	$CONFIG['database']['username'] = 'db_user';
	$CONFIG['database']['password'] = 'db_password';
	
	# Enable URL routing
	$CONFIG['routing']['on'] = TRUE;
	$CONFIG['routing']['routes_file'] = '/var/www/gwc-skeleton/conf/routes.config';
	
	# Enable Automatic CSRF Protection (Cross site request forgery)
	# Force every HTML form on the site to pass back the $GWC->CSRF_TOKEN
	$CONFIG['options']['CSRF_protection'] = FALSE;
	
	# Optionally set a header and footer which will automatically be rendered with each template
	$CONFIG['template']['template_header'] = '/var/www/gwc-skeleton/templates/header.html';
	$CONFIG['template']['template_footer'] = '/var/www/gwc-skeleton/templates/footer.html';
	
	# Constants can be defined here which will be available to entire application
	define('__SITE_NAME__','GWC SKELETON');
														
?>