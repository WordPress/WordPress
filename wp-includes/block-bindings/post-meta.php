<?php
/**
 * Post Meta source for the block bindings.
 *
 * @since 6.5.0
 * @package WordPress
 * @subpackage Block Bindings
 */

/**
 * Gets value for Post Meta source.
 *
 * @since 6.5.0
 * @access private
 *
 * @param array $source_args Array containing source arguments used to look up the override value.
 *                           Example: array( "key" => "foo" ).
 * @return mixed The value computed for the source.
 */
function _block_bindings_post_meta_get_value( array $source_args ) {
	if ( ! isset( $source_args['key'] ) ) {
		return null;
	}

	// Use the postId attribute if available.
	if ( isset( $source_args['postId'] ) ) {
		$post_id = $source_args['postId'];
	} else {
		// $block_instance->context['postId'] is not available in the Image block.
		$post_id = get_the_ID();
	}

	// If a post isn't public, we need to prevent unauthorized users from accessing the post meta.
	$post = get_post( $post_id );
	if ( ( ! is_post_publicly_viewable( $post ) && ! current_user_can( 'read_post', $post_id ) ) || post_password_required( $post ) ) {
		return null;
	}

	return get_post_meta( $post_id, $source_args['key'], true );
}

/**
 * Registers Post Meta source in the block bindings registry.
 *
 * @since 6.5.0
 * @access private
 */
function _register_block_bindings_post_meta_source() {
	register_block_bindings_source(
		'core/post-meta',
		array(
			'label'              => _x( 'Post Meta', 'block bindings source' ),
			'get_value_callback' => '_block_bindings_post_meta_get_value',
		)
	);
}

add_action( 'init', '_register_block_bindings_post_meta_source' );
