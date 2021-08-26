<?php
/**
 * Layout block support flag.
 *
 * @package WordPress
 * @since 5.8.0
 */

/**
 * Registers the layout block attribute for block types that support it.
 *
 * @since 5.8.0
 * @access private
 *
 * @param WP_Block_Type $block_type Block Type.
 */
function wp_register_layout_support( $block_type ) {
	$support_layout = block_has_support( $block_type, array( '__experimentalLayout' ), false );
	if ( $support_layout ) {
		if ( ! $block_type->attributes ) {
			$block_type->attributes = array();
		}

		if ( ! array_key_exists( 'layout', $block_type->attributes ) ) {
			$block_type->attributes['layout'] = array(
				'type' => 'object',
			);
		}
	}
}

/**
 * Renders the layout config to the block wrapper.
 *
 * @since 5.8.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_layout_support_flag( $block_content, $block ) {
	$block_type     = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );
	$support_layout = block_has_support( $block_type, array( '__experimentalLayout' ), false );
	if ( ! $support_layout || ! isset( $block['attrs']['layout'] ) ) {
		return $block_content;
	}

	$used_layout = $block['attrs']['layout'];
	if ( isset( $used_layout['inherit'] ) && $used_layout['inherit'] ) {
		$tree           = WP_Theme_JSON_Resolver::get_merged_data();
		$default_layout = _wp_array_get( $tree->get_settings(), array( 'layout' ) );
		if ( ! $default_layout ) {
			return $block_content;
		}
		$used_layout = $default_layout;
	}

	$id           = uniqid();
	$content_size = isset( $used_layout['contentSize'] ) ? $used_layout['contentSize'] : null;
	$wide_size    = isset( $used_layout['wideSize'] ) ? $used_layout['wideSize'] : null;

	$all_max_width_value  = $content_size ? $content_size : $wide_size;
	$wide_max_width_value = $wide_size ? $wide_size : $content_size;

	// Make sure there is a single CSS rule, and all tags are stripped for security.
	$all_max_width_value  = safecss_filter_attr( explode( ';', $all_max_width_value )[0] );
	$wide_max_width_value = safecss_filter_attr( explode( ';', $wide_max_width_value )[0] );

	$style = '';
	if ( $content_size || $wide_size ) {
		$style  = ".wp-container-$id > * {";
		$style .= 'max-width: ' . esc_html( $all_max_width_value ) . ';';
		$style .= 'margin-left: auto !important;';
		$style .= 'margin-right: auto !important;';
		$style .= '}';

		$style .= ".wp-container-$id > .alignwide { max-width: " . esc_html( $wide_max_width_value ) . ';}';

		$style .= ".wp-container-$id .alignfull { max-width: none; }";
	}

	$style .= ".wp-container-$id .alignleft { float: left; margin-right: 2em; }";
	$style .= ".wp-container-$id .alignright { float: right; margin-left: 2em; }";

	// This assumes the hook only applies to blocks with a single wrapper.
	// I think this is a reasonable limitation for that particular hook.
	$content = preg_replace(
		'/' . preg_quote( 'class="', '/' ) . '/',
		'class="wp-container-' . $id . ' ',
		$block_content,
		1
	);

	return $content . '<style>' . $style . '</style>';
}

// Register the block support.
WP_Block_Supports::get_instance()->register(
	'layout',
	array(
		'register_attribute' => 'wp_register_layout_support',
	)
);
add_filter( 'render_block', 'wp_render_layout_support_flag', 10, 2 );

/**
 * For themes without theme.json file, make sure
 * to restore the inner div for the group block
 * to avoid breaking styles relying on that div.
 *
 * @since 5.8.0
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 *
 * @return string Filtered block content.
 */
function wp_restore_group_inner_container( $block_content, $block ) {
	$group_with_inner_container_regex = '/(^\s*<div\b[^>]*wp-block-group(\s|")[^>]*>)(\s*<div\b[^>]*wp-block-group__inner-container(\s|")[^>]*>)((.|\S|\s)*)/';

	if (
		'core/group' !== $block['blockName'] ||
		WP_Theme_JSON_Resolver::theme_has_support() ||
		1 === preg_match( $group_with_inner_container_regex, $block_content )
	) {
		return $block_content;
	}

	$replace_regex   = '/(^\s*<div\b[^>]*wp-block-group[^>]*>)(.*)(<\/div>\s*$)/ms';
	$updated_content = preg_replace_callback(
		$replace_regex,
		static function( $matches ) {
			return $matches[1] . '<div class="wp-block-group__inner-container">' . $matches[2] . '</div>' . $matches[3];
		},
		$block_content
	);
	return $updated_content;
}

add_filter( 'render_block', 'wp_restore_group_inner_container', 10, 2 );
