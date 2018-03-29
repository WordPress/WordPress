<?php

class UM_Access {

	function __construct() {
	
		$this->redirect_handler = false;
		$this->allow_access = false;

		add_action('template_redirect',  array(&$this, 'template_redirect'), 1000 );
		
	}
	
	
	/**
	 * Set custom access actions and redirection
	 */
	function template_redirect() {
		global $post, $ultimatemember;

		do_action('um_access_global_settings');

		do_action('um_access_user_custom_homepage');
		
		do_action('um_access_frontpage_per_role');
		
		do_action('um_access_homepage_per_role');
		
		do_action('um_access_category_settings');

		do_action('um_access_tags_settings');
	
		do_action('um_access_post_settings');

		if ( $this->redirect_handler && $this->allow_access == false &&  ( ! um_is_core_page('login') || um_is_core_page('login') && is_user_logged_in() ) ) {
			
			// login page add protected page automatically

			if ( strstr( $this->redirect_handler, um_get_core_page('login') ) ){
				$curr = $ultimatemember->permalinks->get_current_url();
				$this->redirect_handler = esc_url(  add_query_arg('redirect_to', urlencode_deep($curr), $this->redirect_handler) );
			}
			
			wp_redirect( $this->redirect_handler );
		
		}
		
	}
	
	
	/**
	 * Get custom access settings meta
	 * @param  integer $post_id 
	 * @return array
	 */
	function get_meta( $post_id ) {
		global $post;
		$meta = get_post_custom( $post_id );
		if ( isset( $meta ) && is_array( $meta ) ) {
			foreach ($meta as $k => $v){
				if ( strstr($k, '_um_') ) {
					$k = str_replace('_um_', '', $k);
					$array[$k] = $v[0];
				}
			}
		}
		if ( isset( $array ) )
			return (array)$array;
		else
			return array('');
	}

	/**
	 * Sets a custom access referer in a redirect URL
	 * @param string $url    
	 * @param string $referer 
	 */
	function set_referer( $url, $referer ){

		$enable_referer = apply_filters("um_access_enable_referer", false );
		if( ! $enable_referer ) return $url;

		$url = add_query_arg('um_ref',$referer, $url);
		return $url;
	}

}