<?php
/**
 * This class handles all functions that changes data structures and moving files 
 */
class UM_Upgrade {

	function __construct() {
		$um_last_version_upgrade = get_option('um_last_version_upgrade');

		if( ! $um_last_version_upgrade || $um_last_version_upgrade != ultimatemember_version ){
			add_action( 'admin_init', array($this,'packages'),10);
		}
		
	}

	/**
	 * Load packages
	 */
	public function packages(  ){
		
		 $file_path = plugin_dir_path ( __FILE__ ).'packages/'.ultimatemember_version.'.php';
		 
		 if( file_exists( $file_path ) ){
		 	include_once( $file_path );
		 	update_option( 'um_last_version_upgrade', ultimatemember_version );
		 }
	}

}

new UM_Upgrade();
