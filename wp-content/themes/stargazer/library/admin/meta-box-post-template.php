<?php
/**
 * Adds the template meta box to the post editing screen for public post types.  This feature allows users and 
 * devs to create custom templates for any post type, not just pages as default in WordPress core.  The 
 * functions in this file create the template meta box and save the template chosen by the user when the 
 * post is saved.  This file is only used if the theme supports the 'hybrid-core-template-hierarchy' feature.
 *
 * @package    HybridCore
 * @subpackage Admin
 * @author     Justin Tadlock <justin@justintadlock.com>
 * @copyright  Copyright (c) 2008 - 2014, Justin Tadlock
 * @link       http://themehybrid.com/hybrid-core
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

/* Add the post template meta box on the 'add_meta_boxes' hook. */
add_action( 'add_meta_boxes', 'hybrid_meta_box_post_add_template',    10, 2 );
add_action( 'add_meta_boxes', 'hybrid_meta_box_post_remove_template', 10, 2 );

/* Save the post template meta box data on the 'save_post' hook. */
add_action( 'save_post',       'hybrid_meta_box_post_save_template', 10, 2 );
add_action( 'add_attachment',  'hybrid_meta_box_post_save_template'        );
add_action( 'edit_attachment', 'hybrid_meta_box_post_save_template'        );

/**
 * Adds the post template meta box for all public post types, excluding the 'page' post type since WordPress 
 * core already handles page templates.
 *
 * @since  1.2.0
 * @access public
 * @param  string  $post_type
 * @param  object  $post
 * @return void
 */
function hybrid_meta_box_post_add_template( $post_type, $post ) {

	/* Get the post templates. */
	$templates = hybrid_get_post_templates( $post_type );

	/* If no post templates were found for this post type, don't add the meta box. */
	if ( empty( $templates ) )
		return;

	$post_type_object = get_post_type_object( $post_type );

	/* Only add meta box if current user can edit, add, or delete meta for the post. */
	if ( ( true === $post_type_object->public ) && ( current_user_can( 'edit_post_meta', $post->ID ) || current_user_can( 'add_post_meta', $post->ID ) || current_user_can( 'delete_post_meta', $post->ID ) ) )
		add_meta_box( 'hybrid-core-post-template', __( 'Template', 'hybrid-core' ), 'hybrid_meta_box_post_display_template', $post_type, 'side', 'default' );
}

/**
 * Remove the meta box from some post types.
 *
 * @since  1.3.0
 * @access public
 * @param  string $post_type The post type of the current post being edited.
 * @param  object $post      The current post being edited.
 * @return void
 */ 
function hybrid_meta_box_post_remove_template( $post_type, $post ) {

	/* Removes meta box from pages since this is a built-in WordPress feature. */
	if ( 'page' == $post_type )
		remove_meta_box( 'hybrid-core-post-template', 'page', 'side' );

	/* Removes meta box from the bbPress 'topic' post type. */
	elseif ( function_exists( 'bbp_get_topic_post_type' ) && bbp_get_topic_post_type() == $post_type )
		remove_meta_box( 'hybrid-core-post-template', bbp_get_topic_post_type(), 'side' );

	/* Removes meta box from the bbPress 'reply' post type. */
	elseif ( function_exists( 'bbp_get_reply_post_type' ) && bbp_get_reply_post_type() == $post_type )
		remove_meta_box( 'hybrid-core-post-template', bbp_get_reply_post_type(), 'side' );
}

/**
 * Displays the post template meta box.
 *
 * @since  1.2.0
 * @access public
 * @param  object  $object
 * @param  array   $box
 * @return void
 */
function hybrid_meta_box_post_display_template( $object, $box ) {

	/* Get the post type object. */
	$post_type_object = get_post_type_object( $object->post_type );

	/* Get a list of available custom templates for the post type. */
	$templates = hybrid_get_post_templates( $object->post_type );

	wp_nonce_field( basename( __FILE__ ), 'hybrid-core-post-meta-box-template' ); ?>

	<p>
		<?php if ( 0 != count( $templates ) ) { ?>
			<select name="hybrid-post-template" id="hybrid-post-template" class="widefat">
				<option value=""></option>
				<?php foreach ( $templates as $label => $template ) { ?>
					<option value="<?php echo esc_attr( $template ); ?>" <?php selected( esc_attr( get_post_meta( $object->ID, "_wp_{$post_type_object->name}_template", true ) ), esc_attr( $template ) ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
			</select>
		<?php } ?>
	</p>
<?php
}

/**
 * Saves the post template meta box settings as post metadata. Note that this meta is sanitized using the 
 * hybrid_sanitize_meta() callback function prior to being saved.
 *
 * @since  1.2.0
 * @access public
 * @param  int      $post_id The ID of the current post being saved.
 * @param  object   $post    The post object currently being saved.
 * @return void|int
 */
function hybrid_meta_box_post_save_template( $post_id, $post = '' ) {

	/* Fix for attachment save issue in WordPress 3.5. @link http://core.trac.wordpress.org/ticket/21963 */
	if ( !is_object( $post ) )
		$post = get_post();

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['hybrid-core-post-meta-box-template'] ) || !wp_verify_nonce( $_POST['hybrid-core-post-meta-box-template'], basename( __FILE__ ) ) )
		return $post_id;

	/* Return here if the template is not set. There's a chance it won't be if the post type doesn't have any templates. */
	if ( !isset( $_POST['hybrid-post-template'] ) )
		return $post_id;

	/* Get the posted meta value. */
	$new_meta_value = $_POST['hybrid-post-template'];

	/* Set the $meta_key variable based off the post type name. */
	$meta_key = "_wp_{$post->post_type}_template";

	/* Get the meta value of the meta key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If there is no new meta value but an old value exists, delete it. */
	if ( current_user_can( 'delete_post_meta', $post_id ) && '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );

	/* If a new meta value was added and there was no previous value, add it. */
	elseif ( current_user_can( 'add_post_meta', $post_id, $meta_key ) && $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( current_user_can( 'edit_post_meta', $post_id ) && $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );
}
