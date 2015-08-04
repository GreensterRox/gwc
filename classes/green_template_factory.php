<?php

Class green_template_factory {
	
	function __construct() {
		
   	}
	
	public static function create($name,$logger,$args){
		include_once('green_template.php');
		$TEMPLATE_OBJECT = new green_template($name,$logger,$args['directory']);
		if(isset($args['template_header']) && !empty($args['template_header'])){
			$TEMPLATE_OBJECT->setHeaderTemplate($args['template_header']);
		}
		if(isset($args['template_footer']) && !empty($args['template_footer'])){
			$TEMPLATE_OBJECT->setFooterTemplate($args['template_footer']);
		}
		return $TEMPLATE_OBJECT;
	}
};

?>