<?php 

add_filter("um_localize_permalink_filter","um_localize_permalink_filter",10,3);
function um_localize_permalink_filter( $core_pages,  $page_id, $profile_url ){
	global $ultimatemember;

	 	if ( function_exists('icl_get_current_language') && icl_get_current_language() != icl_get_default_language() ) {
			if ( get_the_ID() > 0 && get_post_meta( get_the_ID(), '_um_wpml_user', true ) == 1 ) {
				$profile_url = get_permalink( get_the_ID() );
			}
		}

		// WPML compatibility
		if ( function_exists('icl_object_id') ) {
			$language_code = ICL_LANGUAGE_CODE;
			$lang_post_id = icl_object_id( $page_id , 'page', true, $language_code );

			 if($lang_post_id != 0) {
		        $profile_url = get_permalink( $lang_post_id );
		    }else {
		        // No page found, it's most likely the homepage
		        global $sitepress;
		        $profile_url = $sitepress->language_url( $language_code );
		    }

		}

		return $profile_url;

}

add_filter('um_core_page_id_filter','um_core_page_id_filter');
function um_core_page_id_filter( $page_id ){

	return $page_id;
}

/**
 * Adds correct profile url to WPML language switcher
 */
add_filter( 'icl_ls_languages','um_core_page_wpml_permalink', 10, 1 );
function um_core_page_wpml_permalink( $array ){
	global $ultimatemember, $sitepress;
	if( ! um_is_core_page("user") ) return $array;
	if( ! defined("ICL_LANGUAGE_CODE") ) return $array;
	if( ! function_exists('icl_object_id') ) return $array;

	// Permalink base
	$permalink_base = um_get_option('permalink_base');

	// Get user slug
	$profile_slug = get_user_meta( um_profile_id(), "um_user_profile_url_slug_{$permalink_base}", true );
	$current_language = ICL_LANGUAGE_CODE;		
	foreach ( $array as $lang_code => $arr ) {
		$sitepress->switch_lang( $lang_code );
		$user_page = um_get_core_page("user");
	
		$array[ $lang_code ]['url'] = "{$user_page}{$profile_slug}/";
	}

	$sitepress->switch_lang( $current_language );

	return $array;
}