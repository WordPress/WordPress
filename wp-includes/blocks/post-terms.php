<?php
/**
 * Server-side rendering of the `core/post-terms` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/post-terms` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 * @return string Returns the filtered post terms for the current post wrapped inside "a" tags.
 */
function render_block_core_post_terms( $attributes, $content, $block ) {
	if ( ! isset( $block->context['postId'] ) || ! isset( $attributes['term'] ) ) {
		return '';
	}

	if ( ! is_taxonomy_viewable( $attributes['term'] ) ) {
		return '';
	}

	$post_terms = get_the_terms( $block->context['postId'], $attributes['term'] );
	if ( is_wp_error( $post_terms ) || empty( $post_terms ) ) {
		return '';
	}

	$classes = 'taxonomy-' . $attributes['term'];
	if ( isset( $attributes['textAlign'] ) ) {
		$classes .= ' has-text-align-' . $attributes['textAlign'];
	}

	$separator = empty( $attributes['separator'] ) ? ' ' : $attributes['separator'];

	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => $classes ) );

	$prefix = "<div $wrapper_attributes>";
	if ( isset( $attributes['prefix'] ) && $attributes['prefix'] ) {
		$prefix .= '<span class="wp-block-post-terms__prefix">' . $attributes['prefix'] . '</span>';
	}

	$suffix = '</div>';
	if ( isset( $attributes['suffix'] ) && $attributes['suffix'] ) {
		$suffix = '<span class="wp-block-post-terms__suffix">' . $attributes['suffix'] . '</span>' . $suffix;
	}

	return get_the_term_list(
		$block->context['postId'],
		$attributes['term'],
		wp_kses_post( $prefix ),
		'<span class="wp-block-post-terms__separator">' . esc_html( $separator ) . '</span>',
		wp_kses_post( $suffix )
	);
}

/**
 * Registers the `core/post-terms` block on the server.
 */
function register_block_core_post_terms() {
	$taxonomies = get_taxonomies(
		array(
			'public'       => true,
			'show_in_rest' => true,
		),
		'objects'
	);

	// Split the available taxonomies to `built_in` and custom ones,
	// in order to prioritize the `built_in` taxonomies at the
	// search results.
	$built_ins         = array();
	$custom_variations = array();

	// Create and register the eligible taxonomies variations.
	foreach ( $taxonomies as $taxonomy ) {
		$variation = array(
			'name'        => $taxonomy->name,
			'title'       => $taxonomy->label,
			/* translators: %s: taxonomy's label */
			'description' => sprintf( __( 'Display the assigned taxonomy: %s' ), $taxonomy->label ),
			'attributes'  => array(
				'term' => $taxonomy->name,
			),
			'isActive'    => array( 'term' ),
		);
		// Set the category variation as the default one.
		if ( 'category' === $taxonomy->name ) {
			$variation['isDefault'] = true;
		}
		if ( $taxonomy->_builtin ) {
			$built_ins[] = $variation;
		} else {
			$custom_variations[] = $variation;
		}
	}

	register_block_type_from_metadata(
		__DIR__ . '/post-terms',
		array(
			'render_callback' => 'render_block_core_post_terms',
			'variations'      => array_merge( $built_ins, $custom_variations ),
		)
	);
}
add_action( 'init', 'register_block_core_post_terms' );
