<?php

class UM_Logout {

	function __construct() {
		
		add_action('template_redirect', array(&$this, 'logout_page'), 10000 );
		
	}
	
	/***
	***	@Logout via logout page
	***/
	function logout_page() {

		global $sitepress, $ultimatemember, $post;

		$language_code 		= '';
		$current_page_ID    = get_the_ID();
		$logout_page_id 	= $ultimatemember->permalinks->core['logout'];
		$has_translation    = false;
		$trid 				= 0;
		$not_default_lang 	= false;
				
		if( is_home() || is_front_page() ){
			return;
		}

		if ( function_exists('icl_object_id') || function_exists('icl_get_current_language')  ) {

				if( function_exists('icl_get_current_language') ){
					$language_code = icl_get_current_language();
				}else if( function_exists('icl_object_id') && defined('ICL_LANGUAGE_CODE') ){ // checks if WPML exists
					$language_code = ICL_LANGUAGE_CODE;
				}

				$has_translation = true;

				if( function_exists('icl_object_id')  && defined('ICL_LANGUAGE_CODE') && isset( $sitepress ) ){ // checks if WPML exists
					$trid = $sitepress->get_element_trid(  $current_page_ID  );
				}

				if( icl_get_default_language() !== $language_code ){
					$not_default_lang = true;
				}else{
					$language_code = '';
				}
		
		}
		
		

		if ( um_is_core_page('logout') || ( $trid > 0 && $has_translation && $trid == $logout_page_id && $not_default_lang )  ) {
			
			if ( is_user_logged_in() ) {
				
				if ( isset( $_REQUEST['redirect_to'] ) && $_REQUEST['redirect_to'] !== '' ) {
					wp_logout();
					session_unset();
					exit( wp_redirect( $_REQUEST['redirect_to'] ) );
				} else if ( um_user('after_logout') == 'redirect_home' ) {
					wp_logout();
					session_unset();
					exit( wp_redirect( home_url( $language_code ) ) );
				} else {
					wp_logout();
					session_unset();
					exit( wp_redirect( um_user('logout_redirect_url') ) );
					
				}

			} else {
				exit( wp_redirect( home_url( $language_code ) ) );
			}
			
		}
		
	}

}