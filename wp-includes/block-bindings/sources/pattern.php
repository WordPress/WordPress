<?php
/**
 * The "pattern" source for the Block Bindings API. This source is used by the
 * Pattern Overrides.
 *
 * @since 6.5.0
 * @package WordPress
 */
function pattern_source_callback( $source_attrs, $block_instance, $attribute_name ) {
	if ( ! _wp_array_get( $block_instance->attributes, array( 'metadata', 'id' ), false ) ) {
		return null;
	}
	$block_id = $block_instance->attributes['metadata']['id'];
	return _wp_array_get( $block_instance->context, array( 'pattern/overrides', $block_id, $attribute_name ), null );
}


/**
 * Registers the "pattern" source for the Block Bindings API.
 *
 * @access private
 * @since 6.5.0
 */
function _register_block_bindings_pattern_overrides_source() {
	register_block_bindings_source(
		'core/pattern-overrides',
		array(
			'label'              => _x( 'Pattern Overrides', 'block bindings source' ),
			'get_value_callback' => 'pattern_source_callback',
		)
	);
}

add_action( 'init', '_register_block_bindings_pattern_overrides_source' );
