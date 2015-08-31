<?php
/**
 * @package WPSEO\Admin
 */

if ( ! defined( 'WPSEO_VERSION' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit();
}

/**
 * @todo this whole thing should probably be a proper class.
 */

/**
 * Convenience function to JSON encode and echo resuls and then die
 *
 * @param array $results
 */
function wpseo_ajax_json_echo_die( $results ) {
	echo json_encode( $results );
	die();
}

/**
 * Function used from AJAX calls, takes it variables from $_POST, dies on exit.
 */
function wpseo_set_option() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-setoption' );

	$option = sanitize_text_field( filter_input( INPUT_POST, 'option' ) );
	if ( $option !== 'page_comments' ) {
		die( '-1' );
	}

	update_option( $option, 0 );
	die( '1' );
}

add_action( 'wp_ajax_wpseo_set_option', 'wpseo_set_option' );

/**
 * Function used to remove the admin notices for several purposes, dies on exit.
 */
function wpseo_set_ignore() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-ignore' );

	$ignore_key = sanitize_text_field( filter_input( INPUT_POST, 'option' ) );

	$options                          = get_option( 'wpseo' );
	$options[ 'ignore_' . $ignore_key ] = true;
	update_option( 'wpseo', $options );

	die( '1' );
}

add_action( 'wp_ajax_wpseo_set_ignore', 'wpseo_set_ignore' );

/**
 * Hides the after-update notification until the next update for a specific user.
 */
function wpseo_dismiss_about() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-dismiss-about' );

	update_user_meta( get_current_user_id(), 'wpseo_seen_about_version' , WPSEO_VERSION );

	die( '1' );
}

add_action( 'wp_ajax_wpseo_dismiss_about', 'wpseo_dismiss_about' );

/**
 * Hides the default tagline notice for a specific user.
 */
function wpseo_dismiss_tagline_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-dismiss-tagline-notice' );

	update_user_meta( get_current_user_id(), 'wpseo_seen_tagline_notice', 'seen' );

	die( '1' );
}

add_action( 'wp_ajax_wpseo_dismiss_tagline_notice', 'wpseo_dismiss_tagline_notice' );

/**
 * Function used to delete blocking files, dies on exit.
 */
function wpseo_kill_blocking_files() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-blocking-files' );

	$message = 'There were no files to delete.';
	$options = get_option( 'wpseo' );
	if ( is_array( $options['blocking_files'] ) && $options['blocking_files'] !== array() ) {
		$message       = 'success';
		$files_removed = 0;
		foreach ( $options['blocking_files'] as $k => $file ) {
			if ( ! @unlink( $file ) ) {
				$message = __( 'Some files could not be removed. Please remove them via FTP.', 'wordpress-seo' );
			}
			else {
				unset( $options['blocking_files'][ $k ] );
				$files_removed ++;
			}
		}
		if ( $files_removed > 0 ) {
			update_option( 'wpseo', $options );
		}
	}

	die( $message );
}

add_action( 'wp_ajax_wpseo_kill_blocking_files', 'wpseo_kill_blocking_files' );

/**
 * Retrieve the suggestions from the Google Suggest API and return them to be
 * used in the suggest box within the plugin. Dies on exit.
 */
function wpseo_get_suggest() {
	check_ajax_referer( 'wpseo-get-suggest' );

	$term   = urlencode( filter_input( INPUT_GET, 'term' ) );
	$result = wp_remote_get( 'https://www.google.com/complete/search?output=toolbar&q=' . $term );

	$return_arr = array();

	if ( ! is_wp_error( $result ) ) {
		preg_match_all( '`suggestion data="([^"]+)"/>`u', $result['body'], $matches );

		if ( isset( $matches[1] ) && ( is_array( $matches[1] ) && $matches[1] !== array() ) ) {
			foreach ( $matches[1] as $match ) {
				$return_arr[] = html_entity_decode( $match, ENT_COMPAT, 'UTF-8' );
			}
		}
	}
	wpseo_ajax_json_echo_die( $return_arr );
}

add_action( 'wp_ajax_wpseo_get_suggest', 'wpseo_get_suggest' );

/**
 * Used in the editor to replace vars for the snippet preview
 */
function wpseo_ajax_replace_vars() {
	global $post;
	check_ajax_referer( 'wpseo-replace-vars' );

	$post = get_post( intval( filter_input( INPUT_POST, 'post_id' ) ) );
	$omit = array( 'excerpt', 'excerpt_only', 'title' );
	echo wpseo_replace_vars( stripslashes( filter_input( INPUT_POST, 'string' ) ), $post, $omit );
	die;
}

add_action( 'wp_ajax_wpseo_replace_vars', 'wpseo_ajax_replace_vars' );

/**
 * Save an individual SEO title from the Bulk Editor.
 */
function wpseo_save_title() {
	wpseo_save_what( 'title' );
}

add_action( 'wp_ajax_wpseo_save_title', 'wpseo_save_title' );

/**
 * Save an individual meta description from the Bulk Editor.
 */
function wpseo_save_description() {
	wpseo_save_what( 'metadesc' );
}

add_action( 'wp_ajax_wpseo_save_metadesc', 'wpseo_save_description' );

/**
 * Save titles & descriptions
 *
 * @param string $what
 */
function wpseo_save_what( $what ) {
	check_ajax_referer( 'wpseo-bulk-editor' );

	$new      = filter_input( INPUT_POST, 'new_value' );
	$post_id  = intval( filter_input( INPUT_POST, 'wpseo_post_id' ) );
	$original = filter_input( INPUT_POST, 'existing_value' );

	$results = wpseo_upsert_new( $what, $post_id, $new, $original );

	wpseo_ajax_json_echo_die( $results );
}

/**
 * Helper function to update a post's meta data, returning relevant information
 * about the information updated and the results or the meta update.
 *
 * @param int    $post_id
 * @param string $new_meta_value
 * @param string $orig_meta_value
 * @param string $meta_key
 * @param string $return_key
 *
 * @return string
 */
function wpseo_upsert_meta( $post_id, $new_meta_value, $orig_meta_value, $meta_key, $return_key ) {

	$post_id                  = intval( $post_id );
	$sanitized_new_meta_value = wp_strip_all_tags( $new_meta_value );
	$orig_meta_value          = wp_strip_all_tags( $orig_meta_value );

	$upsert_results = array(
		'status'                 => 'success',
		'post_id'                => $post_id,
		"new_{$return_key}"      => $new_meta_value,
		"original_{$return_key}" => $orig_meta_value,
	);

	$the_post = get_post( $post_id );
	if ( empty( $the_post ) ) {

		$upsert_results['status']  = 'failure';
		$upsert_results['results'] = __( 'Post doesn\'t exist.', 'wordpress-seo' );

		return $upsert_results;
	}

	$post_type_object = get_post_type_object( $the_post->post_type );
	if ( ! $post_type_object ) {

		$upsert_results['status']  = 'failure';
		$upsert_results['results'] = sprintf( __( 'Post has an invalid Post Type: %s.', 'wordpress-seo' ), $the_post->post_type );

		return $upsert_results;
	}

	if ( ! current_user_can( $post_type_object->cap->edit_posts ) ) {

		$upsert_results['status']  = 'failure';
		$upsert_results['results'] = sprintf( __( 'You can\'t edit %s.', 'wordpress-seo' ), $post_type_object->label );

		return $upsert_results;
	}

	if ( ! current_user_can( $post_type_object->cap->edit_others_posts ) && $the_post->post_author != get_current_user_id() ) {

		$upsert_results['status']  = 'failure';
		$upsert_results['results'] = sprintf( __( 'You can\'t edit %s that aren\'t yours.', 'wordpress-seo' ), $post_type_object->label );

		return $upsert_results;

	}

	if ( $sanitized_new_meta_value === $orig_meta_value && $sanitized_new_meta_value !== $new_meta_value ) {
		$upsert_results['status']  = 'failure';
		$upsert_results['results'] = __( 'You have used HTML in your value which is not allowed.', 'wordpress-seo' );

		return $upsert_results;
	}

	$res = update_post_meta( $post_id, $meta_key, $sanitized_new_meta_value );

	$upsert_results['status']  = ( $res !== false ) ? 'success' : 'failure';
	$upsert_results['results'] = $res;

	return $upsert_results;
}

/**
 * Save all titles sent from the Bulk Editor.
 */
function wpseo_save_all_titles() {
	wpseo_save_all( 'title' );
}

add_action( 'wp_ajax_wpseo_save_all_titles', 'wpseo_save_all_titles' );

/**
 * Save all description sent from the Bulk Editor.
 */
function wpseo_save_all_descriptions() {
	wpseo_save_all( 'metadesc' );
}

add_action( 'wp_ajax_wpseo_save_all_descriptions', 'wpseo_save_all_descriptions' );

/**
 * Utility function to save values
 *
 * @param string $what
 */
function wpseo_save_all( $what ) {
	check_ajax_referer( 'wpseo-bulk-editor' );

	// @todo the WPSEO Utils class can't filter arrays in POST yet.
	$new_values      = $_POST['items'];
	$original_values = $_POST['existing_items'];

	$results = array();

	if ( is_array( $new_values ) && $new_values !== array() ) {
		foreach ( $new_values as $post_id => $new_value ) {
			$original_value = $original_values[ $post_id ];
			$results[]      = wpseo_upsert_new( $what, $post_id, $new_value, $original_value );
		}
	}
	wpseo_ajax_json_echo_die( $results );
}

/**
 * Insert a new value
 *
 * @param string $what
 * @param int    $post_id
 * @param string $new
 * @param string $original
 *
 * @return string
 */
function wpseo_upsert_new( $what, $post_id, $new, $original ) {
	$meta_key = WPSEO_Meta::$meta_prefix . $what;

	return wpseo_upsert_meta( $post_id, $new, $original, $meta_key, $what );
}

/**
 * Create an export and return the URL
 */
function wpseo_get_export() {

	$include_taxonomy = ( filter_input( INPUT_POST, 'include_taxonomy' ) === 'true' );
	$export           = new WPSEO_Export( $include_taxonomy );

	wpseo_ajax_json_echo_die( $export->get_results() );
}

add_action( 'wp_ajax_wpseo_export', 'wpseo_get_export' );

/**
 * Handles the posting of a new FB admin.
 */
function wpseo_add_fb_admin() {
	check_ajax_referer( 'wpseo_fb_admin_nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	$facebook_social = new Yoast_Social_Facebook();

	wp_die( $facebook_social->add_admin( filter_input( INPUT_POST, 'admin_name' ), filter_input( INPUT_POST, 'admin_id' ) ) );
}

add_action( 'wp_ajax_wpseo_add_fb_admin', 'wpseo_add_fb_admin' );

// Crawl Issue Manager AJAX hooks.
new WPSEO_GSC_Ajax;

new Yoast_Dashboard_Widget();
