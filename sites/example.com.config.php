<?php
	
	# Full path to template directory
	$CONFIG['template']['directory'] = '/path/to/your/application/templates/';
	
	# DB Creds
	$CONFIG['database']['server'] = 'localhost';
	$CONFIG['database']['name'] = 'db_name';
	$CONFIG['database']['username'] = 'db_user';
	$CONFIG['database']['password'] = 'db_password';
	
	# Run-time options
	# debug spews verbose run time messages to application log
	# application log lives here: /var/log/green_framework
	$CONFIG['options']['debug'] = true;
	
	# Plugins ! Have as many as you want.
	# Have GWC Run application logic 
	# Example for a UserAuth Plugin
	$CONFIG['plugin']['UserAuth']	 = array(	
	# REQUIRED. Plugin source script
	'include_file' 			=> '/path/to/your/plugin/script.php', 
	# REQUIRED: Your plugin script should define an object called $UserAuth
	'object_to_retrieve' 	=> 'UserAuth',	
	#OPTIONAL: GWC Will call this method on your defined object before serving the current page.
	'run_method'			=> 'authorise'
	);
														
?>