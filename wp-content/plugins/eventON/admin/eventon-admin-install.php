<?php
/**
 * EventON Install
 *
 * Plugin install script which adds default pages to WordPress. Runs on activation and upgrade.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/Install
 * @version     0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function eventon_create_page( $slug, $option, $page_title = '', $page_content = '', $post_parent = 0 ) {
	global $wpdb;

	$option_value = get_option( $option );

	if ( $option_value > 0 && get_post( $option_value ) )
		return;

	$page_found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM " . $wpdb->posts . " WHERE post_name = %s LIMIT 1;", $slug ) );
	if ( $page_found ) {
		if ( ! $option_value )
			update_option( $option, $page_found );
		return;
	}

	$page_data = array(
        'post_status' 		=> 'publish',
        'post_type' 		=> 'page',
        'post_author' 		=> 1,
        'post_name' 		=> $slug,
        'post_title' 		=> $page_title,
        'post_content' 		=> $page_content,
        'post_parent' 		=> $post_parent,
        'comment_status' 	=> 'closed'
    );
    $page_id = wp_insert_post( $page_data );

    update_option( $option, $page_id );
}


/**
 * Create pages that the plugin relies on, storing page id's in variables.
 */
function eventon_create_pages() {

	// Events Main page
    eventon_create_page( esc_sql( _x( 'event-directory', 'page_slug', 'eventon' ) ), 'eventon_events_page_id', __( 'Events', 'eventon' ), '' );

}