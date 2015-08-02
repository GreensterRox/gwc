<?php
	
	# full path to template directory
	$CONFIG['template']['directory'] = '/home/dperry/Sandbox/project-x/templates/';
	
	# DB Creds
	$CONFIG['database']['server'] = 'localhost';
	$CONFIG['database']['name'] = 'projectx';
	$CONFIG['database']['username'] = 'projectx';
	$CONFIG['database']['password'] = 'pr0j3ctx';
	
	# Run-time options
	# debug spews verbose run time messages to application log
	# application log lives here: /var/log/green_framework
	$CONFIG['options']['debug'] = true;
	$CONFIG['options']['CSRF_protection'] = TRUE;	
	
	# Plugins ! Have as many as you want.
	# Have GWC Run application logic 
	# Example for a UserAuth Plugin
	# Have GWC Run application logic
    $CONFIG['plugin']['UserAuth'] = array(
    	'include_file'       => '/home/dperry/Sandbox/project-x/lib/Registration/Authorisation.php',
    	'object_to_retrieve' => 'UserAuth'
    );
   # $CONFIG['plugin']['URL_Router'] = array( 
   # 	'include_file'       => '/home/dperry/Sandbox/project-x/lib/Routing/Routing.php',
   # 	'object_to_retrieve' => 'URL_Router',
   # 	'run_method'         => 'runApplicationRoute'
   # );
    # Enable friendly URL routes
    $CONFIG['routing']['on'] = TRUE;
    $CONFIG['routing']['routes_file'] = '/home/dperry/Sandbox/project-x/conf/routes.config';
                                                                   
    #####################################
    ## Constants available to entire application
    ## can be defined here
    #####################################
    define('__SUPPORT__EMAIL__','2015@voteworld.co.uk');
?>
