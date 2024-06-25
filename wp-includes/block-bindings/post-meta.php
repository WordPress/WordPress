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
 * @param array    $source_args    Array containing source arguments used to look up the override value.
 *                                 Example: array( "key" => "foo" ).
 * @param WP_Block $block_instance The block instance.
 * @return mixed The value computed for the source.
 */
function _block_bindings_post_meta_get_value( array $source_args, $block_instance ) {
	if ( empty( $source_args['key'] ) ) {
		return null;
	}

	if ( empty( $block_instance->context['postId'] ) ) {
		return null;
	}
	$post_id = $block_instance->context['postId'];

	// If a post isn't public, we need to prevent unauthorized users from accessing the post meta.
	$post = get_post( $post_id );
	if ( ( ! is_post_publicly_viewable( $post ) && ! current_user_can( 'read_post', $post_id ) ) || post_password_required( $post ) ) {
		return null;
	}

	// Check if the meta field is protected.
	if ( is_protected_meta( $source_args['key'], 'post' ) ) {
		return null;
	}

	// Check if the meta field is registered to be shown in REST.
	$meta_keys = get_registered_meta_keys( 'post', $block_instance->context['postType'] );
	// Add fields registered for all subtypes.
	$meta_keys = array_merge( $meta_keys, get_registered_meta_keys( 'post', '' ) );
	if ( empty( $meta_keys[ $source_args['key'] ]['show_in_rest'] ) ) {
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
			'uses_context'       => array( 'postId', 'postType' ),
		)
	);
}

add_action( 'init', '_register_block_bindings_post_meta_source' );
