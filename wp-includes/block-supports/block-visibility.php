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

	if ( ! $block_type || ! block_has_support( $block_type, 'visibility', true ) ) {
		return $block_content;
	}

	$block_visibility = $block['attrs']['metadata']['blockVisibility'] ?? null;

	if ( false === $block_visibility ) {
		return '';
	}

	if ( is_array( $block_visibility ) && ! empty( $block_visibility ) ) {
		$viewport_config = $block_visibility['viewport'] ?? null;

		if ( ! is_array( $viewport_config ) || empty( $viewport_config ) ) {
			return $block_content;
		}
		/*
		 * Viewport size definitions are in several places in WordPress packages.
		 * The following are taken from: https://github.com/WordPress/gutenberg/blob/trunk/packages/base-styles/_breakpoints.scss
		 * The array is in a future, potential JSON format, and will be centralized
		 * as the feature is developed.
		 *
		 * Viewport sizes as array items are defined sequentially. The first item's size is the max value.
		 * Each subsequent item starts after the previous size (using > operator), and its size is the max.
		 * The last item starts after the previous size (using > operator), and it has no max.
		 */
		$viewport_sizes = array(
			array(
				'name' => 'Mobile',
				'slug' => 'mobile',
				'size' => '480px',
			),
			array(
				'name' => 'Tablet',
				'slug' => 'tablet',
				'size' => '782px',
			),
			array(
				'name' => 'Desktop',
				'slug' => 'desktop',
				/*
				 * Note: the last item in the $viewport_sizes array does not technically require a 'size' key,
				 * as the last item's media query is calculated using `width > previous size`.
				 * The last item is present for validating the attribute values, and in order to indicate
				 * that this is the final viewport size, and to calculate the previous media query accordingly.
				 */
			),
		);

		/*
		 * Build media queries from viewport size definitions using the CSS range syntax.
		 * Could be absorbed into the style engine,
		 * as well as classname building, and declaration of the display property, if required.
		 */
		$viewport_media_queries = array();
		$previous_size          = null;
		foreach ( $viewport_sizes as $index => $viewport_size ) {
			// First item: width <= size.
			if ( 0 === $index ) {
				$viewport_media_queries[ $viewport_size['slug'] ] = "@media (width <= {$viewport_size['size']})";
			} elseif ( count( $viewport_sizes ) - 1 === $index && $previous_size ) {
				// Last item: width > previous size.
				$viewport_media_queries[ $viewport_size['slug'] ] = "@media (width > $previous_size)";
			} else {
				// Middle items: previous size < width <= size.
				$viewport_media_queries[ $viewport_size['slug'] ] = "@media ({$previous_size} < width <= {$viewport_size['size']})";
			}

			$previous_size = $viewport_size['size'] ?? null;
		}

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
				$block_content = $processor->get_updated_html();
			}
		}
	}

	return $block_content;
}

add_filter( 'render_block', 'wp_render_block_visibility_support', 10, 2 );
