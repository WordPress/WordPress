<?php

class UM_Modal {

	function __construct() {
	
		add_action('wp_footer', array(&$this, 'load_modal_content'), 9);
	
	}

	/***
	***	@Load modal content
	***/
	function load_modal_content(){
		global 	$ultimatemember;
		
		if ( !is_admin() ) {
			foreach( glob( um_path . 'templates/modal/*.php' ) as $modal_content) {
				include_once $modal_content;
			}
		}

	}
	
}