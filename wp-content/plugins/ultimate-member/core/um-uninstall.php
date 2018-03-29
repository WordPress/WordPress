<?php

class UM_Uninstall {

	function __construct() {

	}
	
	/***
	***	@remove UM
	***/
	function remove_um() {
		global $ultimatemember;
		
		foreach( wp_load_alloptions() as $k => $v ) {
		
			if ( substr( $k, 0, 3 ) == 'um_' ) {
				
				if ( $k == 'um_core_pages' ) {
					$v = unserialize( $v );
					foreach( $v as $post_id ) {
						wp_delete_post( $post_id, 1 );
					}
				}
				
				delete_option( $k );
				
			}
			
		}
		
		$forms = get_posts( array( 'post_type' => 'um_form', 'numberposts'   => -1 ) );
		foreach( $forms as $form ) {wp_delete_post( $form->ID, 1 );}
		
		$directories = get_posts( array( 'post_type' => 'um_directory', 'numberposts'   => -1 ) );
		foreach( $directories as $directory ) {wp_delete_post( $directory->ID, 1 );}
		
		$roles = get_posts( array( 'post_type' => 'um_role', 'numberposts'   => -1 ) );
		foreach( $roles as $role ) {wp_delete_post( $role->ID, 1 );}

		if ( is_plugin_active( um_plugin ) ) {
			deactivate_plugins( um_plugin );
		}
		
		exit( wp_redirect( admin_url('plugins.php') ) );

	}

}