<?php
/**
 * Block visibility block support flag.
 *
 * @package WordPress
 * @since 6.9.0
 */

/**
 * Render nothing if the block is hidden, or add viewport visibility styles.
 *
 * @since 6.9.0
 * @since 7.0.0 Added support for viewport visibility.
 * @access private
 *
 * @param string $block_content Rendered block content.
 * @param array  $block         Block object.
 * @return string Filtered block content.
 */
function wp_render_block_visibility_support( $block_content, $block ) {
	$block_type = WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] );

	if ( ! $block_type ) {
		return $block_content;
	}

	$block_visibility = $block['attrs']['metadata']['blockVisibility'] ?? null;

	// Hide the block whenever the value is boolean false, regardless of the
	// block's current visibility support. This prevents blocks that previously
	// supported visibility from unintentionally appearing on the front end
	// after their support was disabled.
	if ( false === $block_visibility ) {
		return '';
	}

	if ( ! block_has_support( $block_type, 'visibility', true ) ) {
		return $block_content;
	}

	if ( is_array( $block_visibility ) && ! empty( $block_visibility ) ) {
		$viewport_config = $block_visibility['viewport'] ?? null;

		if ( ! is_array( $viewport_config ) || empty( $viewport_config ) ) {
			return $block_content;
		}
		$viewport_settings      = wp_get_global_settings( array( 'viewport' ) );
		$viewport_media_queries = WP_Theme_JSON::get_viewport_media_queries(
			$viewport_settings,
			array(
				'include_desktop' => true,
			)
		);

		/*
		 * Viewport media queries are keyed by style-state names (`@mobile`,
		 * `@tablet`, and `@desktop`). Block visibility metadata and generated
		 * classes use plain viewport names, so map the keys at this boundary.
		 */
		$block_visibility_media_queries = array();
		foreach ( $viewport_media_queries as $viewport_state => $media_query ) {
			$block_visibility_media_queries[ ltrim( $viewport_state, '@' ) ] = $media_query;
		}
		$viewport_media_queries = $block_visibility_media_queries;

		$hidden_on = array();

		// Collect which viewport the block is hidden on (only known viewport sizes).
		foreach ( $viewport_config as $viewport_config_size => $is_visible ) {
			if ( false === $is_visible && isset( $viewport_media_queries[ $viewport_config_size ] ) ) {
				$hidden_on[] = $viewport_config_size;
			}
		}

		// If no viewport sizes have visibility set to false, return unchanged.
		if ( empty( $hidden_on ) ) {
			return $block_content;
		}

		// Maintain consistent order of viewport sizes for class name generation.
		sort( $hidden_on );

		$css_rules   = array();
		$class_names = array();

		foreach ( $hidden_on as $hidden_viewport_size ) {
			/*
			 * If these values ever become user-defined,
			 * they should be sanitized and kebab-cased.
			 */
			$visibility_class = 'wp-block-hidden-' . $hidden_viewport_size;
			$class_names[]    = $visibility_class;
			$css_rules[]      = array(
				'selector'     => '.' . $visibility_class,
				'declarations' => array(
					'display' => 'none !important',
				),
				'rules_group'  => $viewport_media_queries[ $hidden_viewport_size ],
			);
		}

		wp_style_engine_get_stylesheet_from_css_rules(
			$css_rules,
			array(
				'context'  => 'block-supports',
				'prettify' => false,
			)
		);

		if ( ! empty( $block_content ) ) {
			$processor = new WP_HTML_Tag_Processor( $block_content );
			if ( $processor->next_tag() ) {
				$processor->add_class( implode( ' ', $class_names ) );

				/*
				 * Set all IMG tags to be `fetchpriority=auto` so that wp_get_loading_optimization_attributes() won't add
				 * `fetchpriority=high` or increment the media count to affect whether subsequent IMG tags get `loading=lazy`.
				 */
				do {
					if ( 'IMG' === $processor->get_tag() ) {
						$processor->set_attribute( 'fetchpriority', 'auto' );
					}
				} while ( $processor->next_tag() );
				$block_content = $processor->get_updated_html();
			}
		}
	}

	return $block_content;
}

add_filter( 'render_block', 'wp_render_block_visibility_support', 10, 2 );
