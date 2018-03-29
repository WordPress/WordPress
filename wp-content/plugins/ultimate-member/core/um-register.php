<?php

class UM_Register {
	
	function __construct(){

		add_action("um_after_register_fields",  array( $this, 'add_nonce' ) );
		add_action("um_submit_form_register", array( $this, 'verify_nonce'), 1, 1);
	}

	public function add_nonce(){
		wp_nonce_field( 'um_register_form' );
	}

	public function verify_nonce( $args ){
		global $ultimatemember;
        
		$allow_nonce_verification = apply_filters("um_register_allow_nonce_verification", true );

		if( ! $allow_nonce_verification  ){
			return $args;
		}
		
		if ( ! wp_verify_nonce( $args['_wpnonce'], 'um_register_form' ) || empty( $args['_wpnonce'] ) || ! isset( $args['_wpnonce'] ) ) {
			wp_die('Invalid Nonce.');
		}

		return $args;
	}

}