<?php
/**
 * Functions used to duplicate event
 *
 * Based on 'Duplicate Post' (http://www.lopo.it/duplicate-post-plugin/) by Enrico Battocchi
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	eventon/Admin
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Duplicate event action.
 *
 * @access public
 * @return void
 */
function eventon_duplicate_event() {
	if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_page' == $_REQUEST['action'] ) ) ) {
		wp_die(__( 'No event to duplicate has been supplied!', 'eventon' ));
	}

	// Get the original page
	$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	check_admin_referer( 'eventon-duplicate-event_' . $id );
	$post = eventon_get_event_to_duplicate($id);

	// Copy the page and insert it
	if (isset($post) && $post!=null) {
		$new_id = eventon_create_duplicate_from_event($post);

		// If you have written a plugin which uses non-WP database tables to save
		// information about a page you can hook this action to dupe that data.
		do_action( 'eventon_duplicate_product', $new_id, $post );

		// Redirect to the edit screen for the new draft page
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
		exit;
	} else {
		wp_die(__( 'Event creation failed, could not find original event:', 'eventon' ) . ' ' . $id);
	}
}


/**
 * Get a product from the database to duplicate
 *
 * @access public
 * @param mixed $id
 * @return void
 */
function eventon_get_event_to_duplicate($id) {
	global $wpdb;
	$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
	if (isset($post->post_type) && $post->post_type == "revision"){
		$id = $post->post_parent;
		$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
	}
	return $post[0];
}


/**
 * Function to create the duplicate of the product.
 *
 * @access public
 * @param mixed $post
 * @param int $parent (default: 0)
 * @param string $post_status (default: '')
 * @return void
 */
function eventon_create_duplicate_from_event( $post, $parent = 0, $post_status = '' ) {
	global $wpdb;

	$new_post_author 	= wp_get_current_user();
	$new_post_date 		= current_time('mysql');
	$new_post_date_gmt 	= get_gmt_from_date($new_post_date);

	if ( $parent > 0 ) {
		$post_parent		= $parent;
		$post_status 		= $post_status ? $post_status : 'publish';
		$suffix 			= '';
	} else {
		$post_parent		= $post->post_parent;
		$post_status 		= $post_status ? $post_status : 'draft';
		$suffix 			= ' ' . __( '(Copy)', 'eventon' );
	}

	$new_post_type 			= $post->post_type;
	$post_content    		= str_replace("'", "''", $post->post_content);
	$post_content_filtered 	= str_replace("'", "''", $post->post_content_filtered);
	$post_excerpt    		= str_replace("'", "''", $post->post_excerpt);
	$post_title      		= str_replace("'", "''", $post->post_title).$suffix;
	$post_name       		= str_replace("'", "''", $post->post_name);
	$comment_status  		= str_replace("'", "''", $post->comment_status);
	$ping_status     		= str_replace("'", "''", $post->ping_status);

	// Insert the new template in the post table
	$wpdb->query(
			"INSERT INTO $wpdb->posts
			(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, post_type, comment_status, ping_status, post_password, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_mime_type)
			VALUES
			('$new_post_author->ID', '$new_post_date', '$new_post_date_gmt', '$post_content', '$post_content_filtered', '$post_title', '$post_excerpt', '$post_status', '$new_post_type', '$comment_status', '$ping_status', '$post->post_password', '$post->to_ping', '$post->pinged', '$new_post_date', '$new_post_date_gmt', '$post_parent', '$post->menu_order', '$post->post_mime_type')");

	$new_post_id = $wpdb->insert_id;

	// Copy the taxonomies
	eventon_duplicate_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

	// Copy the meta information
	eventon_duplicate_post_meta( $post->ID, $new_post_id );

	// Copy the children (variations)
	if ( $children_products =& get_children( 'post_parent='.$post->ID.'&post_type=product_variation' ) ) {

		if ($children_products) foreach ($children_products as $child) {

			eventon_create_duplicate_from_event( eventon_get_event_to_duplicate( $child->ID ), $new_post_id, $child->post_status );

		}

	}

	return $new_post_id;
}


/**
 * Copy the taxonomies of a post to another post
 *
 * @access public
 * @param mixed $id
 * @param mixed $new_id
 * @param mixed $post_type
 * @return void
 */
function eventon_duplicate_post_taxonomies($id, $new_id, $post_type) {
	global $wpdb;
	$taxonomies = get_object_taxonomies($post_type); //array("category", "post_tag");
	foreach ($taxonomies as $taxonomy) {
		$post_terms = wp_get_object_terms($id, $taxonomy);
		$post_terms_count = sizeof( $post_terms );
		for ($i=0; $i<$post_terms_count; $i++) {
			wp_set_object_terms($new_id, $post_terms[$i]->slug, $taxonomy, true);
		}
	}
}


/**
 * Copy the meta information of a post to another post
 *
 * @access public
 * @param mixed $id
 * @param mixed $new_id
 * @return void
 */
function eventon_duplicate_post_meta($id, $new_id) {
	global $wpdb;
	$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id");

	if (count($post_meta_infos)!=0) {
		$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		foreach ($post_meta_infos as $meta_info) {
			$meta_key = $meta_info->meta_key;
			$meta_value = addslashes($meta_info->meta_value);
			$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
		}
		$sql_query.= implode(" UNION ALL ", $sql_query_sel);
		$wpdb->query($sql_query);
	}
}
?>