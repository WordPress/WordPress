<?php

function wp_print_styles( $handles = false ) {
	do_action( 'wp_print_styles' );
	if ( '' === $handles ) // for wp_head
		$handles = false;

	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') ) {
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$wp_styles = new WP_Styles();
	}

	return $wp_styles->do_items( $handles );
}

function wp_register_style( $handle, $src, $deps = array(), $ver = false, $media = false ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	$wp_styles->add( $handle, $src, $deps, $ver, $media );
}

function wp_deregister_style( $handle ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	$wp_styles->remove( $handle );
}

function wp_enqueue_style( $handle, $src = false, $deps = array(), $ver = false, $media = false ) {
	global $wp_styles;
	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}
	$wp_styles->enqueue( $handle );
}
