<?php
/**
 * WordPress Bookmark Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function add_link() {
	return edit_link();
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $link_id
 * @return unknown
 */
function edit_link( $link_id = '' ) {
	if (!current_user_can( 'manage_links' ))
		wp_die( __( 'Cheatin&#8217; uh?' ));

	$_POST['link_url'] = esc_html( $_POST['link_url'] );
	$_POST['link_url'] = esc_url($_POST['link_url']);
	$_POST['link_name'] = esc_html( $_POST['link_name'] );
	$_POST['link_image'] = esc_html( $_POST['link_image'] );
	$_POST['link_rss'] = esc_url($_POST['link_rss']);
	if ( !isset($_POST['link_visible']) || 'N' != $_POST['link_visible'] )
		$_POST['link_visible'] = 'Y';

	if ( !empty( $link_id ) ) {
		$_POST['link_id'] = $link_id;
		return wp_update_link( $_POST);
	} else {
		return wp_insert_link( $_POST);
	}
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @return unknown
 */
function get_default_link_to_edit() {
	if ( isset( $_GET['linkurl'] ) )
		$link->link_url = esc_url( $_GET['linkurl']);
	else
		$link->link_url = '';

	if ( isset( $_GET['name'] ) )
		$link->link_name = esc_attr( $_GET['name']);
	else
		$link->link_name = '';

	$link->link_visible = 'Y';

	return $link;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $link_id
 * @return unknown
 */
function wp_delete_link( $link_id ) {
	global $wpdb;

	do_action( 'delete_link', $link_id );

	wp_delete_object_term_relationships( $link_id, 'link_category' );

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->links WHERE link_id = %d", $link_id ) );

	do_action( 'deleted_link', $link_id );

	clean_bookmark_cache( $link_id );

	return true;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $link_id
 * @return unknown
 */
function wp_get_link_cats( $link_id = 0 ) {

	$cats = wp_get_object_terms( $link_id, 'link_category', array('fields' => 'ids') );

	return array_unique( $cats );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $link_id
 * @return unknown
 */
function get_link_to_edit( $link_id ) {
	return get_bookmark( $link_id, OBJECT, 'edit' );
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $linkdata
 * @return unknown
 */
function wp_insert_link( $linkdata, $wp_error = false ) {
	global $wpdb;

	$defaults = array( 'link_id' => 0, 'link_name' => '', 'link_url' => '', 'link_rating' => 0 );

	$linkdata = wp_parse_args( $linkdata, $defaults );
	$linkdata = sanitize_bookmark( $linkdata, 'db' );

	extract( stripslashes_deep( $linkdata ), EXTR_SKIP );

	$update = false;

	if ( !empty( $link_id ) )
		$update = true;

	if ( trim( $link_name ) == '' ) {
		if ( trim( $link_url ) != '' ) {
			$link_name = $link_url;
		} else {
			return 0;
		}
	}

	if ( trim( $link_url ) == '' )
		return 0;

	if ( empty( $link_rating ) )
		$link_rating = 0;

	if ( empty( $link_image ) )
		$link_image = '';

	if ( empty( $link_target ) )
		$link_target = '';

	if ( empty( $link_visible ) )
		$link_visible = 'Y';

	if ( empty( $link_owner ) )
		$link_owner = get_current_user_id();

	if ( empty( $link_notes ) )
		$link_notes = '';

	if ( empty( $link_description ) )
		$link_description = '';

	if ( empty( $link_rss ) )
		$link_rss = '';

	if ( empty( $link_rel ) )
		$link_rel = '';

	// Make sure we set a valid category
	if ( ! isset( $link_category ) ||0 == count( $link_category ) || !is_array( $link_category ) ) {
		$link_category = array( get_option( 'default_link_category' ) );
	}

	if ( $update ) {
		if ( false === $wpdb->update( $wpdb->links, compact('link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_rating', 'link_rel', 'link_notes', 'link_rss'), compact('link_id') ) ) {
			if ( $wp_error )
				return new WP_Error( 'db_update_error', __( 'Could not update link in the database' ), $wpdb->last_error );
			else
				return 0;
		}
	} else {
		if ( false === $wpdb->insert( $wpdb->links, compact('link_url', 'link_name', 'link_image', 'link_target', 'link_description', 'link_visible', 'link_owner', 'link_rating', 'link_rel', 'link_notes', 'link_rss') ) ) {
			if ( $wp_error )
				return new WP_Error( 'db_insert_error', __( 'Could not insert link into the database' ), $wpdb->last_error );
			else
				return 0;
		}
		$link_id = (int) $wpdb->insert_id;
	}

	wp_set_link_cats( $link_id, $link_category );

	if ( $update )
		do_action( 'edit_link', $link_id );
	else
		do_action( 'add_link', $link_id );

	clean_bookmark_cache( $link_id );

	return $link_id;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $link_id
 * @param unknown_type $link_categories
 */
function wp_set_link_cats( $link_id = 0, $link_categories = array() ) {
	// If $link_categories isn't already an array, make it one:
	if ( !is_array( $link_categories ) || 0 == count( $link_categories ) )
		$link_categories = array( get_option( 'default_link_category' ) );

	$link_categories = array_map( 'intval', $link_categories );
	$link_categories = array_unique( $link_categories );

	wp_set_object_terms( $link_id, $link_categories, 'link_category' );

	clean_bookmark_cache( $link_id );
}	// wp_set_link_cats()

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param unknown_type $linkdata
 * @return unknown
 */
function wp_update_link( $linkdata ) {
	$link_id = (int) $linkdata['link_id'];

	$link = get_bookmark( $link_id, ARRAY_A );

	// Escape data pulled from DB.
	$link = add_magic_quotes( $link );

	// Passed link category list overwrites existing category list if not empty.
	if ( isset( $linkdata['link_category'] ) && is_array( $linkdata['link_category'] )
			 && 0 != count( $linkdata['link_category'] ) )
		$link_cats = $linkdata['link_category'];
	else
		$link_cats = $link['link_category'];

	// Merge old and new fields with new fields overwriting old ones.
	$linkdata = array_merge( $link, $linkdata );
	$linkdata['link_category'] = $link_cats;

	return wp_insert_link( $linkdata );
}

?>
