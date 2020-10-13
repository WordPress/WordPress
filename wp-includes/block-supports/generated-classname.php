<?php
/**
 * Generated classname block support flag.
 *
 * @package WordPress
 */

/**
 * Get the generated classname from a given block name.
 *
 * @param  string $block_name Block Name.
 * @return string Generated classname.
 */
function wp_get_block_default_classname( $block_name ) {
	// Generated HTML classes for blocks follow the `wp-block-{name}` nomenclature.
	// Blocks provided by WordPress drop the prefixes 'core/' or 'core-' (historically used in 'core-embed/').
	$classname = 'wp-block-' . preg_replace(
		'/^core-/',
		'',
		str_replace( '/', '-', $block_name )
	);

	/**
	 * Filters the default block className for server rendered blocks.
	 *
	 * @param string     $class_name The current applied classname.
	 * @param string     $block_name The block name.
	 */
	$classname = apply_filters( 'block_default_classname', $classname, $block_name );

	return $classname;
}

/**
 * Add the generated classnames to the output.
 *
 * @param  array         $attributes       Comprehensive list of attributes to be applied.
 * @param  array         $block_attributes Block attributes.
 * @param  WP_Block_Type $block_type       Block Type.
 *
 * @return array Block CSS classes and inline styles.
 */
function wp_apply_generated_classname_support( $attributes, $block_attributes, $block_type ) {
	$has_generated_classname_support = true;
	if ( property_exists( $block_type, 'supports' ) ) {
		$has_generated_classname_support = wp_array_get( $block_type->supports, array( 'className' ), true );
	}
	if ( $has_generated_classname_support ) {
		$block_classname = wp_get_block_default_classname( $block_type->name );

		if ( $block_classname ) {
			$attributes['css_classes'][] = $block_classname;
		}
	}

	return $attributes;
}
