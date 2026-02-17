<?php
/**
 * Auto-register block support.
 *
 * @package WordPress
 * @since 7.0.0
 */

/**
 * Marks user-defined attributes for auto-generated inspector controls.
 *
 * This filter runs during block type registration, before the WP_Block_Type
 * is instantiated. Block supports add their attributes AFTER the block type
 * is created (via {@see WP_Block_Supports::register_attributes()}), so any attributes
 * present at this stage are user-defined.
 *
 * The marker tells generateFieldsFromAttributes() which attributes should
 * get auto-generated inspector controls. Attributes are excluded if they:
 * - Have a 'source' (HTML-derived, edited inline not via inspector)
 * - Have role 'local' (internal state, not user-configurable)
 * - Have an unsupported type (only 'string', 'number', 'integer', 'boolean' are supported)
 * - Were added by block supports (added after this filter runs)
 *
 * @since 7.0.0
 * @access private
 *
 * @param array<string, mixed> $args Array of arguments for registering a block type.
 * @return array<string, mixed> Modified block type arguments.
 */
function wp_mark_auto_generate_control_attributes( array $args ): array {
	if ( empty( $args['attributes'] ) || ! is_array( $args['attributes'] ) ) {
		return $args;
	}

	$has_auto_register = ! empty( $args['supports']['autoRegister'] );
	if ( ! $has_auto_register ) {
		return $args;
	}

	foreach ( $args['attributes'] as $attr_key => $attr_schema ) {
		// Skip HTML-derived attributes (edited inline, not via inspector).
		if ( ! empty( $attr_schema['source'] ) ) {
			continue;
		}
		// Skip internal attributes (not user-configurable).
		if ( isset( $attr_schema['role'] ) && 'local' === $attr_schema['role'] ) {
			continue;
		}
		// Skip unsupported types (only 'string', 'number', 'integer', 'boolean' are supported).
		$type = $attr_schema['type'] ?? null;
		if ( ! in_array( $type, array( 'string', 'number', 'integer', 'boolean' ), true ) ) {
			continue;
		}
		$args['attributes'][ $attr_key ]['autoGenerateControl'] = true;
	}

	return $args;
}

// Priority 5 to mark original attributes before other filters (priority 10+) might add their own.
add_filter( 'register_block_type_args', 'wp_mark_auto_generate_control_attributes', 5 );
