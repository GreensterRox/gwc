<?php
	
	# Full path to template directory
	$CONFIG['template']['directory'] = '/data/green_software/fullStackSocial/templates/';
	
	# DB Creds
	$CONFIG['database']['server'] = 'localhost';
	$CONFIG['database']['name'] = 'gwc_social_cast';
	$CONFIG['database']['username'] = 'soc_cast';
	$CONFIG['database']['password'] = 'hulkw4nn45m45h1t';
	
	# Run-time options
	$CONFIG['options']['debug'] = TRUE;
	
	# Have GWC Run application logic 
	# Router Plugin MUST be last one loaded
	#$CONFIG['plugin']['UserAuth']	 	= array(	'include_file' 			=> '/data/green_software/fullStackSocial/lib/Registration/Authorisation.php',
	#												'object_to_retrieve' 	=> 'UserAuth');

	# Enable friendly URL routes
	#$CONFIG['routing']['on'] = TRUE;
	#$CONFIG['routing']['routes_file'] = '/data/green_software/fullStackSocial/conf/routes.config';
	
	# Enable Automatic CSRF Protection
	# Forces each and every form on the site to pass back the GWC->CSRF_TOKEN
	# in a hidden form field called 'gwc_csrf'
	#$CONFIG['options']['CSRF_protection'] = FALSE;
	
	# Optionally set a header and footer which will be automatically rendered with every page
	#$CONFIG['template']['template_header'] = '/data/green_software/fullStackSocial/templates/Client/header.html';
	#$CONFIG['template']['template_footer'] = '/data/green_software/fullStackSocial/templates/Client/footer.html';
?>