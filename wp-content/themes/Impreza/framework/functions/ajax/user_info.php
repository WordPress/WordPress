<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Ajax method for wooCommerce cart.
 */
add_action( 'wp_ajax_nopriv_us_ajax_user_info', 'us_ajax_user_info' );
add_action( 'wp_ajax_us_ajax_user_info', 'us_ajax_user_info' );
function us_ajax_user_info() {

	if ( ! is_user_logged_in() ) {
		wp_send_json_error();
	}

	$current_user = wp_get_current_user();

	$logout_redirect = ( isset( $_POST['logout_redirect'] ) ) ? $_POST['logout_redirect'] : '';

	$result = array(
		'name' => $current_user->display_name,
		'avatar' => get_avatar( get_current_user_id(), '64' ),
		'logout_url' => wp_logout_url( esc_url( $logout_redirect ) ),
	);

	wp_send_json_success( $result );

}
