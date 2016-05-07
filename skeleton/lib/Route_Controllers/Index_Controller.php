<?php
/* This is a controller class - this will run the matching 'route'. 
This is INSTEAD of having a htdocs script
*/
class Index_Controller {
	function __construct($GWC) {
		# GWC varoable is passed into all route controllers by default.
		# You should assign it to a local property in order to use it in your class
		$this->GWC = $GWC;
   }
	public function run() {
		
		print 'This is the index document';
	    #$this->GWC->templatePut('user_display_name',$USER['first_name']);
		#$this->GWC->templatePut('GWC',$this->GWC);
		#$this->GWC->templatePut('title','Welcome To Social Netcast');
		#$this->GWC->render('index.html',FALSE);
	}
}
?>