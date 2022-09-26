<?php
/**
 * Block level presets support.
 *
 * @package WordPress
 * @since 6.1.0
 */

/**
 * Get the class name used on block level presets.
 *
 * @access private
 *
 * @param array $block Block object.
 * @return string      The unique class name.
 */
function _wp_get_presets_class_name( $block ) {
	return 'wp-settings-' . md5( serialize( $block ) );
}

/**
 * Update the block content with block level presets class name.
 *
 * @access private
 *
 * @param  string $block_content Rendered block content.
 * @param  array  $block         Block object.
 * @return string                Filtered block content.
 */
function _wp_add_block_level_presets_class( $block_content, $block ) {
	if ( ! $block_content ) {
		return $block_content;
	}

	// return early if the block doesn't have support for settings.
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	if ( ! block_has_support( $block_type, array( '__experimentalSettings' ), false ) ) {
		return $block_content;
	}

	// return early if no settings are found on the block attributes.
	$block_settings = _wp_array_get( $block, array( 'attrs', 'settings' ), null );
	if ( empty( $block_settings ) ) {
		return $block_content;
	}

	$class_name = _wp_get_presets_class_name( $block );

	// Like the layout hook this assumes the hook only applies to blocks with a single wrapper.
	// Retrieve the opening tag of the first HTML element.
	$html_element_matches = array();
	preg_match( '/<[^>]+>/', $block_content, $html_element_matches, PREG_OFFSET_CAPTURE );
	$first_element = $html_element_matches[0][0];
	// If the first HTML element has a class attribute just add the new class
	// as we do on layout and duotone.
	if ( strpos( $first_element, 'class="' ) !== false ) {
		$content = preg_replace(
			'/' . preg_quote( 'class="', '/' ) . '/',
			'class="' . $class_name . ' ',
			$block_content,
			1
		);
	} else {
		// If the first HTML element has no class attribute we should inject the attribute before the attribute at the end.
		$first_element_offset = $html_element_matches[0][1];
		$content              = substr_replace( $block_content, ' class="' . $class_name . '"', $first_element_offset + strlen( $first_element ) - 1, 0 );
	}

	return $content;
}

/**
 * Render the block level presets stylesheet.
 *
 * @access private
 *
 * @param string|null $pre_render   The pre-rendered content. Default null.
 * @param array       $block The block being rendered.
 *
 * @return null
 */
function _wp_add_block_level_preset_styles( $pre_render, $block ) {
	// Return early if the block has not support for descendent block styles.
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	if ( ! block_has_support( $block_type, array( '__experimentalSettings' ), false ) ) {
		return null;
	}

	// return early if no settings are found on the block attributes.
	$block_settings = _wp_array_get( $block, array( 'attrs', 'settings' ), null );
	if ( empty( $block_settings ) ) {
		return null;
	}

	$class_name = '.' . _wp_get_presets_class_name( $block );

	// the root selector for preset variables needs to target every possible block selector
	// in order for the general setting to override any bock specific setting of a parent block or
	// the site root.
	$variables_root_selector = '*,[class*="wp-block"]';
	$registry                = WP_Block_Type_Registry::get_instance();
	$blocks                  = $registry->get_all_registered();
	foreach ( $blocks as $block_type ) {
		if (
			isset( $block_type->supports['__experimentalSelector'] ) &&
			is_string( $block_type->supports['__experimentalSelector'] )
		) {
			$variables_root_selector .= ',' . $block_type->supports['__experimentalSelector'];
		}
	}
	$variables_root_selector = WP_Theme_JSON::scope_selector( $class_name, $variables_root_selector );

	// Remove any potentially unsafe styles.
	$theme_json_shape  = WP_Theme_JSON::remove_insecure_properties(
		array(
			'version'  => WP_Theme_JSON::LATEST_SCHEMA,
			'settings' => $block_settings,
		)
	);
	$theme_json_object = new WP_Theme_JSON( $theme_json_shape );

	$styles = '';

	// include preset css variables declaration on the stylesheet.
	$styles .= $theme_json_object->get_stylesheet(
		array( 'variables' ),
		null,
		array(
			'root_selector' => $variables_root_selector,
			'scope'         => $class_name,
		)
	);

	// include preset css classes on the the stylesheet.
	$styles .= $theme_json_object->get_stylesheet(
		array( 'presets' ),
		null,
		array(
			'root_selector' => $class_name . ',' . $class_name . ' *',
			'scope'         => $class_name,
		)
	);

	if ( ! empty( $styles ) ) {
		wp_enqueue_block_support_styles( $styles );
	}

	return null;
}

add_filter( 'render_block', '_wp_add_block_level_presets_class', 10, 2 );
add_filter( 'pre_render_block', '_wp_add_block_level_preset_styles', 10, 2 );
